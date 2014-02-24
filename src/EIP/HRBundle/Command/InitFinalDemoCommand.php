<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use EIP\HRBundle\Utils\TestTools;

/**
 * \class InitFinalDemoCommand
 * \brief This command sets up some entities for test purpose
 */
class InitFinalDemoCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('hr:initFinalDemo')
        ->setDescription('Set everything up for the final eip presentation');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        // USERS
        $output->writeln("Creating: Users - Processing.");
        $users = array();

        $output->writeln("Creating: User:'eip', Password:'pass' - Processing.");
        $user = TestTools::getValidUser('eip', 'pass', 'test1@gmail.com', $this->getContainer()->get('security.encoder_factory'));
        $eip = $user;
        $em->persist($user);
        $users[] = $user;
        $output->writeln("Creating: User:'eip' - Done.");

        $output->writeln("Creating: User:'vim42', Password:'pass' - Processing.");
        $user = TestTools::getValidUser('vim42', 'pass', 'test2@gmail.com', $this->getContainer()->get('security.encoder_factory'));
        $em->persist($user);
        $users[] = $user;
        $output->writeln("Creating: User:'vim42' - Done.");

        // create 28 random players
        $usernames = array('val', 'mopi', 'teX', 'Erg', 'Meow', 'leChien', 'warum', 'Noes', 'LePereNoel', 'William',
                            'fora', 'deus', 'jack12', 'genH', 'nerDz', 'polz', 'nastyCat', 'brod', 'flum', 'DEN',
                            'iliu99', 'SKY', 'o_O', 'Paupi', 'Lore', 'Tyty', 'Oly', 'Plac0' );

        foreach (range(0,27) as $i) {
            $user = TestTools::getValidUser($usernames[$i], 'pass', 'rtest'.$i.'@hotmail.com', $this->getContainer()->get('security.encoder_factory'));
            $em->persist($user);
            $users[] = $user;
        }
        $output->writeln("Creating: 28 random users - Done.");

        // GAMES
        $gameNames = array('Valor', 'Demestros', 'Q4', 'Dem');
        $output->writeln("Creating: Games - Processing.");
        $games = array();
        foreach (range(0,3) as $i)
        {
            if ($i == 0)
                $status = \EIP\HRBundle\Entity\HRGame::STATUS_OPENED;
            else if ($i == 3)
                $status = \EIP\HRBundle\Entity\HRGame::STATUS_CLOSED;
            else
                $status = \EIP\HRBundle\Entity\HRGame::STATUS_CAN_SIGNUP;
            $game = TestTools::getValidGame($gameNames[$i], $status);
            $games[] = $game;
            $em->persist($game);
        }
        $games[0]->setOpenedOn(\DateTime::createFromFormat("Y/m/d H:i:s", "2014/02/03 00:00:00"));
        $games[1]->setOpenedOn(\DateTime::createFromFormat("Y/m/d H:i:s", "2014/02/25 00:00:00"));
        $games[2]->setOpenedOn(\DateTime::createFromFormat("Y/m/d H:i:s", "2014/03/13 00:00:00"));

        $output->writeln("Creating: Games - Done.");

        // TOWNS
        // 3 towns per player, 30 players
        $towns = array();
        $output->writeln("Creating: Towns - Processing.");
        foreach ($games as $g) {
            $i = 0; $j = 0;
            $tmpTowns = $this->getContainer()->get('town_factory')->getXFromResourceFile(90);
            foreach ($tmpTowns as $town)
            {
                $town->setGame($g);
                $town->setOwner($users[$j]);
                $em->persist($town);
                $towns[] = $town;
                if (++$i % 3 == 0) $j++;
            }
        }
        $output->writeln("Creating: Towns - Done.");
        // GARRISONS
        $output->writeln("Creating: Garrisons - Processing.");
        $garrisons = array();
        $unitsSchema = $em->getRepository('EIPHRBundle:HRUnitSchema')->findAll();

        foreach ($towns as $t)
        {
                $garrison = TestTools::getValidGarrison($t);
                foreach ($unitsSchema as $schema)
                {
                        for ($i = 0; $i < rand(0, 54); $i++)
                        {
                                $unit = new \EIP\HRBundle\Entity\HRUnit;
                                $unit->setSchema($schema);
                                $unit->setArmy($garrison);
                                $em->persist($unit);
                                $garrison->getUnits()->add($unit);
                        }
                }
                $garrisons[] = $garrison;
                $em->persist($garrison);
        }
        $output->writeln("Creating: Garrisons - Done.");

        // GAMES LINK
        $output->writeln("Creating: GamesLink - Processing.");
        $gls = array();
        $hschemas = $em->getRepository('EIPHRBundle:HRHeroSchema')->findAll();
        foreach ($users as $u)
        {
                foreach ($games as $g)
                {
                        $gl = TestTools::getValidGameLink($u, $g);
                        $resources = $gl->getResources();
                        $resources['water'] = '5000.0';
                        $resources['pureWater'] = '5000.0';
                        $resources['fuel'] = '5000.0';
                        $resources['steel'] = '5000.0';
                        $resources['waterStock'] = '10000';
                        $resources['pureWaterStock'] = '10000';
                        $resources['fuelStock'] = '10000';
                        $resources['steelStock'] = '10000';
                        $gl->setResources($resources);
                        $gls[] = $gl;
                        $em->persist($gl);
                        // hero~
                        $hero = new \EIP\HRBundle\Entity\HRHero();
                        $heroSchema = $hschemas[rand(0, count($hschemas) - 1)];
                        $hero->setSchema($heroSchema);
                        $hero->setGame($g);
                        $hero->setUser($u);
                        $hero->setLevel(1);
                        $gl->setHero($hero);
                        $em->persist($hero);


                        break;
                }
        }
         $em->flush();
        $output->writeln("Creating: Gameslink - Done.");
        //giving 1 item to eip heroes
        $heroes = $em->createQuery("SELECT h, u FROM EIPHRBundle:HRHero h JOIN h.user u WHERE u.username = 'eip'")->getResult();
        $itemschemas = $em->createQuery("SELECT s FROM EIPHRBundle:HRItemSchema s")->getResult();
        foreach ($heroes as $hero)
        {
            $item = new \EIP\HRBundle\Entity\HRItem();
            $item->setSchema($itemschemas[0]);
            $item->setHero($hero);
            $em->persist($item);
        }

        $em->flush();
        $output->writeln("Creating: items for eip heroes - Done.");
        // Quests
        $qss = $em->getRepository('EIPHRBundle:HRQuestSchema')->findAll();
        foreach ($qss as $qs) {
            foreach ($games as $g) {
                $ql = new \EIP\HRBundle\Entity\HRQuestGameLink($qs, $g);
                $em->persist($ql);
            }
        }
        $em->flush();
        $output->writeln("Creating: QuestLinks - Done.");



        //
        $eipTowns = $em->getRepository('EIPHRBundle:HRTown')->findBy(array(
            'owner' => $eip->getId()
        ));
        // add armies to eip player
        foreach($eipTowns as $t)
        {
            foreach (range(0, 2) as $i)
            {
                $army = new \EIP\HRBundle\Entity\HRArmy($eip, $t->getGame(), $t);
                foreach ($unitsSchema as $schema)
                {
                    for ($i = 0; $i < rand(0, 22); $i++)
                    {
                        $unit = new \EIP\HRBundle\Entity\HRUnit;
                        $unit->setSchema($schema);
                        $unit->setArmy($army);
                        $em->persist($unit);
                        $army->getUnits()->add($unit);
                    }
                }
                $em->persist($army);
            }
        }
        $output->writeln("Added armies to 'eip'");
        $em->flush();


        $em->merge($eip);
        // messages
        $senders = array(
            $em->getRepository('EIPHRBundle:HRUser')->findOneBy(array('username' => 'Oly')),
            $em->getRepository('EIPHRBundle:HRUser')->findOneBy(array('username' => 'Tyty')),
        $em->getRepository('EIPHRBundle:HRUser')->findOneBy(array('username' => 'Lore'))
        );

        $msg = new \EIP\HRBundle\Entity\HRMessage();
        $msg->setTitle("Proposition d'alliance");
        $msg->setContent("Salut, je te propose d'entrer dans mon alliance sur Valor ! Contacte moi si ça t'intéresse.");
        $msg->setSender($senders[0]);
        $msg->setReceiver($eip);
        $em->persist($msg);

        $msg = new \EIP\HRBundle\Entity\HRMessage();
        $msg->setTitle("Attaque synchro demain a 21h30");
        $msg->setContent("Hey, on a prevu d'attaquer Meow avec William demain soir, t'es partant ?");
        $msg->setSender($senders[1]);
        $msg->setReceiver($eip);
        $em->persist($msg);

        $msg = new \EIP\HRBundle\Entity\HRMessage();
        $msg->setTitle("Besoin d'aide");
        $msg->setContent("Salut, tu pourrais m'aider a battre genH sur Demestros ? Il me harass depuis 3 jours c'est infernal :(");
        $msg->setSender($senders[2]);
        $msg->setReceiver($eip);
        $em->persist($msg);
        $output->writeln("Added messages");


        // FORUM
        $em = $this->getContainer()->get('doctrine')->getManager();
        // create the forum sections
        $sectionInfos = array('forum.general' => 'forum.general.description',
            'forum.technical' => 'forum.technical.description',
            'forum.everything.else' => 'forum.everything.else.description'
            );
        $repo = $em->getRepository('EIPHRBundle:HRForumSection');
        foreach ($sectionInfos as $key => $value) {
            $e = $repo->findOneBy(array('name' => $key));
            if ($e != null)
            {
                $output->writeln($key." section already exists, passing.");
                continue;
            }
            $section = new \EIP\HRBundle\Entity\HRForumSection();
            $section->setName($key);
            $section->setDescription($value);
            $em->persist($section);
            $output->writeln("creating section: ".$key);
        }
        $em->flush();
        $output->writeln("InitForum: Done.");
        // adding topics
        $generalSection = $em->getRepository('EIPHRBundle:HRForumSection')->findOneBy(array(
            'name' => 'forum.general'
        ));
        $humanReborn = $em->getRepository('EIPHRBundle:HRUser')->findOneBy(array(
            'username' => 'humanReborn'
        ));

        $topic = new \EIP\HRBundle\Entity\HRForumTopic();
        $topic->setTitle("Avant de poster");
        $topic->setSection($generalSection);
        $topic->setUser($humanReborn);
        $topic->setSticky(false);
        $em->persist($topic);

        $topic = new \EIP\HRBundle\Entity\HRForumTopic();
        $topic->setTitle("Vos suggestions");
        $topic->setSection($generalSection);
        $topic->setUser($humanReborn);
        $topic->setSticky(false);
        $em->persist($topic);

        $topic = new \EIP\HRBundle\Entity\HRForumTopic();
        $topic->setTitle("Beta: c'est parti !");
        $topic->setSection($generalSection);
        $topic->setUser($humanReborn);
        $topic->setSticky(false);
        $em->persist($topic);


        $oly = $em->getRepository('EIPHRBundle:HRUser')->findOneByUsername("Oly");
        $lore = $em->getRepository('EIPHRBundle:HRUser')->findOneByUsername("Lore");

        $topic = new \EIP\HRBundle\Entity\HRForumTopic();
        $topic->setTitle("On veut plus de parties !");
        $topic->setSection($generalSection);
        $topic->setUser($oly);
        $topic->setSticky(false);
        $em->persist($topic);

        $em->flush();
        $output->writeln("Forum updated");

        // some buildings in towns
        $waterExtractorSchema = $em->getRepository('EIPHRBundle:HRBuildingSchema')->findOneBy(array('rValue' => 1));
        $barracksSchema = $em->getRepository('EIPHRBundle:HRBuildingSchema')->findOneBy(array('rValue' => 16));

        foreach ($towns as $t) {
            $b = new \EIP\HRBundle\Entity\HRBuilding();
            $b->setSchema($waterExtractorSchema);
            $b->setTown($t);
            $t->getBuildings()->add($b);
            $em->persist($b);

            $b = new \EIP\HRBundle\Entity\HRBuilding();
            $b->setSchema($barracksSchema);
            $b->setTown($t);
            $t->getBuildings()->add($b);
            $em->persist($b);
        }

         $em->flush();
        $output->writeln("Towns' buildings created");

        // battlereports
        $nastyCat = $em->getRepository('EIPHRBundle:HRUser')->findOneByUsername('nastyCat');
        $brod = $em->getRepository('EIPHRBundle:HRUser')->findOneByUsername('brod');
        $game = $em->getRepository('EIPHRBundle:HRGame')->findOneByName('Valor');
        $defendedTown = $em->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'game' => $game->getId(),
            'owner' => $eip->getId()
        ));
        $attackedTown = $em->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'game' => $game->getId(),
            'owner' => $brod->getId()
        ));
        $nastyCatTown = $em->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'game' => $game->getId(),
            'owner' => $nastyCat->getId()
        ));

        $s = $this->getSchemaIdArray($unitsSchema);

        $army1 = $em->getRepository('EIPHRBundle:HRArmy')->findOneBy(array(
            'town' => $defendedTown->getId(),
            'garrison' => true
        ));
        $army2 = new \EIP\HRBundle\Entity\HRArmy($nastyCat, $game, $nastyCatTown);
        foreach (array('mercenary' => 24, 'soldier' => 32, 'buggy' => 10) as $k => $v)
        {
            foreach (range(0, $v) as $i) {
                $u = new \EIP\HRBundle\Entity\HRUnit();
                $u->setArmy($army2);
                $u->setSchema($s[$k]);
                $army2->getUnits()->add($u);
                $em->persist($u);
            }
        }
        $em->persist($army2);
        $em->flush();
        \EIP\HRBundle\Utils\HRTools::resolveFight($army2, $army1, $em, time() - 60*60*48,
            $this->getContainer()->get('translator'));
        $em->flush();


        $army1 = $em->getRepository('EIPHRBundle:HRArmy')->findOneBy(array(
            'town' => $attackedTown->getId(),
            'garrison' => true
        ));
        $army2 = new \EIP\HRBundle\Entity\HRArmy($eip, $game, $defendedTown);
        foreach (array('mercenary' => 56, 'soldier' => 32, 'buggy' => 2) as $k => $v)
        {
            foreach (range(0, $v) as $i) {
                $u = new \EIP\HRBundle\Entity\HRUnit();
                $u->setArmy($army2);
                $u->setSchema($s[$k]);
                $army2->getUnits()->add($u);
                $em->persist($u);
            }
        }
        $em->persist($army2);
         $em->flush();
        \EIP\HRBundle\Utils\HRTools::resolveFight($army2, $army1, $em, time() - 60*60*55,
            $this->getContainer()->get('translator'));
        $em->flush();



        // notifications
        // clan
        $clan = new \EIP\HRBundle\Entity\HRClan();
        $clan->setName("Vroum");
        $clan->setAcronym("VR");
        $clan->setBanner("");
        $clan->setPublicPresentation("Bienvenue sur la page de l'alliance Vroum !");
        $clan->setPrivatePresentation("krkrkr pizza party mardi soir, tous chez Marc !");
        $clan->setRecruitmentStatut(false);
        $clan->setIdGame($game->getId());
        $em->persist($clan);
        $em->flush();

        $rank = new \EIP\HRBundle\Entity\HRClanRank($clan->getId(), "membre", true, true, true, true, true, true);
        $em->persist($rank);
        $em->flush();

        $m = new \EIP\HRBundle\Entity\HRClanMembers($clan->getId(),$eip->getId(), $rank->getId());
        $em->persist($m);

        $m2 = new \EIP\HRBundle\Entity\HRClanMembers($clan->getId(), $oly->getId(), $rank->getId());
        $em->persist($m2);
        $em->flush();
        $output->writeln("Clan.done");

        // technologies
        $ts = $em->getRepository('EIPHRBundle:HRTechnologySchema')->findOneByName('techno.weapon1');
        $t = new \EIP\HRBundle\Entity\HRTechnology();
        $t->setSchema($ts);
        $t->setGame($game);
        $t->setUser($eip);
        $em->persist($t);
        $em->flush();



        $output->writeln("COMPLETED");
    }

    private function getSchemaIdArray($schemas)
    {
        $r = array();
        foreach ($schemas as $s)
        {
            $r[$s->getName()] = $s;
        }
        return $r;
    }

}