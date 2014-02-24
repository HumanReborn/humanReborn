<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use EIP\HRBundle\Utils\TestTools;

/**
 * \class InitGameCommand
 * \brief This command sets up some entities for test purpose
 */
class InitGameCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hr:initGame')
            ->setDescription('Création d\'une partie, d\'un joueur, de villes et de garnisons');
    }

      protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager();


        // USERS
        $output->writeln("Creating: Users - Processing.");
        $users = array();

        $output->writeln("Creating: User:'eip', Password:'pass' - Processing.");
        $user = TestTools::getValidUser('eip', 'pass', 'test1@gmail.com', $this->getContainer()->get('security.encoder_factory'));
        $em->persist($user);
        $users[] = $user;
        $output->writeln("Creating: User:'eip' - Done.");

        $output->writeln("Creating: User:'toto', Password:'pass' - Processing.");
        $user = TestTools::getValidUser('toto', 'pass', 'test2@gmail.com', $this->getContainer()->get('security.encoder_factory'));
        $em->persist($user);
        $users[] = $user;
        $output->writeln("Creating: User:'toto' - Done.");

        // create 28 random players
        foreach (range(0,27) as $i) {
            $user = TestTools::getValidUser('player'.$i, 'pass', 'rtest'.$i.'@hotmail.com', $this->getContainer()->get('security.encoder_factory'));
            $em->persist($user);
            $users[] = $user;
        }
        $output->writeln("Creating: 28 random users - Done.");

        // GAMES
        $output->writeln("Creating: Games - Processing.");
        $games = array();
        foreach (range(1,3) as $i)
        {
            $game = TestTools::getValidGame('Game_Test_'.$i, \EIP\HRBundle\Entity\HRGame::STATUS_OPENED);
            $games[] = $game;
            $em->persist($game);
        }
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
                        $heroSchema = $hschemas[0];
                        $hero->setSchema($heroSchema);
                        $hero->setGame($g);
                        $hero->setUser($u);
                        $gl->setHero($hero);
                        $em->persist($hero);


                        break;
                }
        }
        $em->flush();
        $output->writeln("Creating: Gameslink - Done.");
        //giving 3 items to eip heroes
        $heroes = $em->createQuery("SELECT h, u FROM EIPHRBundle:HRHero h JOIN h.user u WHERE u.username = 'eip'")->getResult();
        $itemschemas = $em->createQuery("SELECT s FROM EIPHRBundle:HRItemSchema s")->getResult();
        foreach ($heroes as $hero)
        {
            $item = new \EIP\HRBundle\Entity\HRItem();
            $item->setSchema($itemschemas[0]);
            $item->setHero($hero);
            $em->persist($item);
            $item = new \EIP\HRBundle\Entity\HRItem();
            $item->setSchema($itemschemas[1]);
            $item->setHero($hero);
            $em->persist($item);
            $item = new \EIP\HRBundle\Entity\HRItem();
            $item->setSchema($itemschemas[2]);
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
    }
}
