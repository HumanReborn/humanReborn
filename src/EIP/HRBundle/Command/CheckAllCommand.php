<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *	\class CheckAllCommand
 *  \brief This command look for completed entities in the queue and add them to the finished entities tables
 */
class CheckAllCommand extends ContainerAwareCommand
{
    protected function configure()
    {

        $this
            ->setName('hr:checkAll')
            ->setDescription('check entity and movement completion');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $i = 0;
        $tr = $this->getContainer()->get('translator');
    	$em = $this->getContainer()->get('doctrine')->getManager();
    	$em->getConnection()->getConfiguration()->setSQLLogger(null); // disable doctrine log (main source of memory leak)
        while (1) {
            $i++;
            $currentTime = time();
            $tBeg = microtime(true);
            $this->buildingCompletion($em, $currentTime, $tr);
            $em->flush();
            $em->clear();
            $this->unitCompletion($em, $currentTime);
            $em->flush();
            $em->clear();
            $this->technologyCompletion($em, $currentTime);
            $em->flush();
            $em->clear();
            $this->movementCompletion($em, $currentTime, $tr);
            $em->flush();
            $em->clear();
            $this->buffCompletion($em, $currentTime);
            $em->flush();
            $em->clear();
            $this->achievements($em);
            $em->flush();
            $em->clear();
            if (($i % 12) == 0) { // 12*5 => one minute
                $this->resources($em, $currentTime);
                $em->flush();
                $em->clear();
                $output->writeln("RESOURCES");
            }
            if (($i % 720) == 0){ // 1 hour
                $i = 0;
                $em->getRepository('EIPHRBundle:HRGame')->updateOpenedGamesList();
                $em->flush();
                $em->clear();
                $output->writeln("RESOURCES");
            }
            $output->writeln("CheckAll Done.");
            $tEnd = microtime(true);
            $sleepTime = 5000000 - (($tEnd - $tBeg) * 1000000);
            gc_collect_cycles(); // force garbage collecting
            usleep($sleepTime);
        }
    }

    private function buffCompletion($em, $currentTime) {
        $completedBuffs = $em->createQuery("
                SELECT b
                FROM EIPHRBundle:HRBuff b
                JOIN b.schema s
                WHERE b.validUntil < :currentTime
                AND s.permanent = false
            ")
                ->setParameter(':currentTime', $currentTime)
                ->getResult();
        foreach ($completedBuffs as $b)
            $em->remove($b);
    }

    public function movementCompletion($em, $currentTime, $tr) {
        $completedMovements = $em->createQuery("
                SELECT m, a, g, u, b, bs
                FROM EIPHRBundle:HRArmyMovement m
                JOIN m.army a
                JOIN a.game g
                JOIN a.user u
                LEFT JOIN a.units b
                LEFT JOIN b.schema bs
                WHERE m.endTime <= :time
                AND m.finished = false
                ORDER BY bs.type ASC
            ")
                ->setParameter(':time', $currentTime)
                ->getResult();

        foreach ($completedMovements as $cmove)
        {
            $cmove->setStartTime(0);
            $cmove->setEndTime(0);
            $cmove->setFromX($cmove->getToX());
            $cmove->setFromY($cmove->getToY());
            $cmove->setFinished(true);
            $cmove->getArmy()->setMoving(false);
            // check if new position is town position ~ fight
            try
            {
                $town = $em->getRepository('EIPHRBundle:HRTown')->getTownAt($cmove->getArmy()->getGame(),
                                    $cmove->getToX(), $cmove->getToY());
            }
            catch (\Exception $ex)
            {
                continue ;
            }
            // if town belongs to enemy, fight
            if ($town->getOwner() != $cmove->getArmy()->getUser())
            {
                // gather stationned armies in the garrison of the town, then resolve the fight
                $defendingArmies = $em->getRepository('EIPHRBundle:HRArmy')->getArmiesAt($cmove->getArmy()->getGame(), $cmove->getToX(), $cmove->getToY());
                $garrison = $defendingArmies[0];
                foreach ($defendingArmies as $tmpArmy)
                {
                    if ($tmpArmy->isGarrison()) continue;
                    foreach ($tmpArmy->getUnits() as $unit)
                    {
                        $unit->setArmy($garrison);
                        $tmpArmy->getUnits()->removeElement($unit);
                        $garrison->getUnits()->add($unit);
                    }
                    $em->remove($tmpArmy);
                }
                \EIP\HRBundle\Utils\HRTools::resolveFight($cmove->getArmy(), $garrison, $em, $currentTime, $tr);
            }
            // else set the town as the home of the army
            else
            {
                $cmove->getArmy()->setTown($town);
                $message = $tr->trans('movement.completed', array('%armyid%' => $cmove->getArmy()->getId(), '%townName%' => $town->getName()), null, $cmove->getArmy()->getUser()->getLocale());
                $notification = \EIP\HRBundle\Entity\HRNotification::createNotification(
                                \EIP\HRBundle\Entity\HRNotification::SUCCESS, $town->getGame(), $town->getOwner(), $message);
                $em->persist($notification);
            }
        }
    }

    public function technologyCompletion($em, $currentTime) {
        $completedTechnologies = $em->createQuery("
                SELECT t, s, u, g
                FROM EIPHRBundle:HRTechnologyQueue t
                JOIN t.schema s
                JOIN t.user u
                JOIN t.game g
                WHERE t.endTime <= :time
            ")
                ->setParameter(':time', $currentTime)
                ->getResult();
        foreach ($completedTechnologies as $ctech)
        {
            $newTech = new \EIP\HRBundle\Entity\HRTechnology();
            $newTech->setSchema($ctech->getSchema());
            $newTech->setGame($ctech->getGame());
            $newTech->setUser($ctech->getUser());
            // if the technology gives a buff, create and persist it here
            if ($ctech->getSchema()->getBuffSchema() != null) {
                $gl = $em->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
                   'user' => $ctech->getUser()->getId(),
                   'game' => $ctech->getGame()->getId(),
                ));
                $technoBuff = new \EIP\HRBundle\Entity\HRBuff($ctech->getSchema()->getBuffSchema(), $gl);
                $em->persist($technoBuff);
            }
            $em->persist($newTech);
            $em->remove($ctech);
        }
    }

    private function unitCompletion($em, $currentTime) {
        $completedUnitss = $em->createQuery("
                SELECT u, s, a, user
                FROM EIPHRBundle:HRUnitQueue u
                JOIN u.schema s
                JOIN u.army a
                JOIN a.user user
                WHERE u.endTime <= :time
            ")
                ->setParameter(':time', $currentTime)
                ->getResult();

        foreach ($completedUnitss as $cunit)
        {
            // insert the completed unit in the unit table
            $newUnit = new \EIP\HRBundle\Entity\HRUnit();
            $newUnit->setSchema($cunit->getSchema());
            $newUnit->setArmy($cunit->getArmy());
            $cunit->getArmy()->getUnits()->add($newUnit);
            // update achievements progression
            $cunit->getArmy()->getUser()->addToAchievementsProgression('nbUnitsRecruited', 1);
            // if the new building produces resources, update the gameLink.resources array
            $em->persist($newUnit);
            // delete the old hrbuildingqueue
            $em->remove($cunit);
        }
    }

    private function buildingCompletion($em, $currentTime, $tr) {
        $completedBuildings = $em->createQuery("
                SELECT b, s, t
                FROM EIPHRBundle:HRBuildingQueue b
                JOIN b.schema s
                JOIN b.town t
                WHERE b.endTime <= :time
            ")
                ->setParameter(':time', $currentTime)
                ->getResult();
        foreach ($completedBuildings as $cbuilding)
        {
            // insert the completed buildings in hrbuildings
            $newBuilding = new \EIP\HRBundle\Entity\HRBuilding();
            $newBuilding->setSchema($cbuilding->getSchema());
            $newBuilding->setTown($cbuilding->getTown());
            //
            $user = $cbuilding->getUser();
            $game = $cbuilding->getGame();
            $gameLink = $em->createQuery("
                SELECT l,q,qs
                FROM EIPHRBundle:HRGameLink l
                LEFT JOIN l.quests q
                LEFT JOIN q.schema qs
                WHERE l.user = :userid
                AND l.game = :gameid
            ")
                ->setParameter(':userid', $user->getId())
                ->setParameter(':gameid', $game->getId())
                ->getSingleResult();

            // if the new building produces resources, update the gameLink.resources array
            if ($cbuilding->getSchema()->isCollecting())
                $gameLink->addCollectingBuilding($cbuilding->getSchema());
            $em->persist($newBuilding);
            // delete the old hrbuildingqueue
            $em->remove($cbuilding);
            // update achievement progress
            $user->addToAchievementsProgression('nbBuildings', 1);
            // check for quest completion
            foreach ($gameLink->getQuests() as $quest) {
                if ($quest->getSchema()->getType() == \EIP\HRBundle\Entity\HRQuestSchema::BUILD
                        && $quest->getState() == \EIP\HRBundle\Entity\HRQuest::STATE_ONGOING)
                {
                    $data = $quest->getData();
                    $data[$cbuilding->getSchema()->getId()] += 1;
                    $quest->setData($data);
                    if ($quest->isCompleted()) {
                        \EIP\HRBundle\Utils\HRTools::completeQuest ($quest, $quest->getUser(),
                                                                    $quest->getGame(), null, $tr, $em);
                    }
                }

            }
        }
    }

    private function achievements(\Doctrine\ORM\EntityManager $em){
        $users = $em->createQuery("
            SELECT u,a,acs
            FROM EIPHRBundle:HRUser u
            LEFT JOIN u.achievements a
            LEFT JOIN a.schema acs
            ")
            ->useQueryCache(true)
            ->getResult();
        $achievements = $em->createQuery("
            SELECT schema
            FROM EIPHRBundle:HRAchievementSchema schema
            ")
            ->useResultCache(true)
            ->getResult();
        foreach ($achievements as $a) {
            foreach ($users as $user) {
                if ($a->isAchieved($user))
                {
                    if (!$user->hasAchieved($a->getId()))
                    {
                        $newAchievement = new \EIP\HRBundle\Entity\HRAchievement();
                        $newAchievement->setSchema($a);
                        $newAchievement->setUser($user);
                        $user->getAchievements()->add($newAchievement);
                        $em->persist($newAchievement);
                    }
                }
            }
        }
    }

    public function resources($em) {
        $gamelinks = $em->createQuery("
                SELECT gl,b,s,h,hs
                FROM EIPHRBundle:HRGameLink gl
                LEFT JOIN gl.buffs b
                LEFT JOIN b.schema s
                JOIN gl.hero h
                JOIN h.schema hs
                WHERE gl.hero IS NOT NULL
            ")->getResult();
        $currentTime = time();
        foreach ($gamelinks as $gl)
            $gl->updateResources($currentTime);
    }

}