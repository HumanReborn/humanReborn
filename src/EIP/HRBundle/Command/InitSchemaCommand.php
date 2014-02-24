<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use EIP\HRBundle\Utils\TestTools;

/**
 * \class InitSchemaCommand
 * \brief This command sets up the schemas of the game
 */
class InitSchemaCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hr:initSchema')
            ->setDescription('Ajout des batiments, des unitées, des héros et des technologies dans la bdd');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Schema: Creation ....");
        $this->buildingSchema($output);
        $this->unitSchema($output);
        $this->heroSchema($output);
        $this->buffAndItemSchema($output);
        $this->technologySchema($output);
        $this->questSchema($output);
        $this->achievementSchema($output);
        $this->createNeutralUser($output);
        $output->writeln("Schema: Created.");
    }

    private function buildingSchema(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $output->writeln("Creating: Buildings Schema - Processing.");

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("water.well");    $nB->setRValue(1);        $nB->setBuildingTime(36);
        $nB->setWaterCost(0);        $nB->setWaterCollectRate(2);
        $nB->setPureWaterCost(0);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(50);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(0);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(0);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('water.well.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("decontamination.center");    $nB->setRValue(2);        $nB->setBuildingTime(72);
        $nB->setWaterCost(0);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(0);    $nB->setPureWaterCollectRate(1);
        $nB->setSteelCost(100);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(0);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(1);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('decontamination.center.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("refinery");    $nB->setRValue(4);        $nB->setBuildingTime(149);
        $nB->setWaterCost(50);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(0);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(100);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(50);            $nB->setFuelCollectRate(2);
        $nB->setBuildingRequirement(1);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('refinery.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("factory");    $nB->setRValue(8);        $nB->setBuildingTime(251);
        $nB->setWaterCost(200);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(0);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(50);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(100);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(4);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('factory.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("barracks");    $nB->setRValue(16);        $nB->setBuildingTime(63);
        $nB->setWaterCost(60);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(20);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(50);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(50);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(1);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('barracks.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("mine");    $nB->setRValue(32);        $nB->setBuildingTime(152);
        $nB->setWaterCost(100);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(40);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(250);        $nB->setSteelCollectRate(1);
        $nB->setFuelCost(100);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(8);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('mine.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("research.center");    $nB->setRValue(64);        $nB->setBuildingTime(219);
        $nB->setWaterCost(150);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(0);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(250);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(300);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(4);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('research.center.desc');
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $nB->setName("factory.advanced");    $nB->setRValue(128);        $nB->setBuildingTime(649);
        $nB->setWaterCost(500);        $nB->setWaterCollectRate(0);
        $nB->setPureWaterCost(350);    $nB->setPureWaterCollectRate(0);
        $nB->setSteelCost(700);        $nB->setSteelCollectRate(0);
        $nB->setFuelCost(900);            $nB->setFuelCollectRate(0);
        $nB->setBuildingRequirement(64);        $nB->setTechnologyRequirement(0);
                $nB->setDescription('factory.advanced.desc');
        $em->persist($nB);

        $em->flush();
        $output->writeln("Creating: Buildings Schema - Done.");
    }

    private function unitSchema(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln("Creating: Units Schema - Processing.");

        $nB = new \EIP\HRBundle\Entity\HRUnitSchema();
        $nB->setName("mercenary");              $nB->setImg(1);    $nB->setBuildingTime(36);
        $nB->setWaterCost(10);                  $nB->setPureWaterCost(2);
        $nB->setSteelCost(0);                   $nB->setFuelCost(0);
        $nB->setBuildingRequirement(0);         $nB->setTechnologyRequirement(0);
        $nB->setHp(50);    $nB->setAttack(6);   $nB->setArmor(3); $nB->setType(0); $nB->setSpeed(10);
        $em->persist($nB);
        $desc = new \EIP\HRBundle\Entity\HRUnitDescription($nB);
        $desc->setContent("mercenary.desc");
        $em->persist($desc);

        $nB = new \EIP\HRBundle\Entity\HRUnitSchema();
        $nB->setName("soldier");        $nB->setImg(2);    $nB->setBuildingTime(72);
        $nB->setWaterCost(12);        $nB->setPureWaterCost(5);
        $nB->setSteelCost(4);        $nB->setFuelCost(0);
        $nB->setBuildingRequirement(16);        $nB->setTechnologyRequirement(16);
        $nB->setHp(80);    $nB->setAttack(10); $nB->setArmor(7); $nB->setType(1); $nB->setSpeed(8);
        $em->persist($nB);
        $desc = new \EIP\HRBundle\Entity\HRUnitDescription($nB);
        $desc->setContent("soldier.desc");
        $em->persist($desc);

        $nB = new \EIP\HRBundle\Entity\HRUnitSchema();
        $nB->setName("buggy");        $nB->setImg(3);    $nB->setBuildingTime(149);
        $nB->setWaterCost(30);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(40);        $nB->setFuelCost(8);
        $nB->setBuildingRequirement(8);        $nB->setTechnologyRequirement(32);
        $nB->setHp(260);    $nB->setAttack(22); $nB->setArmor(18); $nB->setType(3); $nB->setSpeed(80);
        $em->persist($nB);
        $desc = new \EIP\HRBundle\Entity\HRUnitDescription($nB);
        $desc->setContent("buggy.desc");
        $em->persist($desc);

        $nB = new \EIP\HRBundle\Entity\HRUnitSchema();
        $nB->setName("humvee");        $nB->setImg(4);    $nB->setBuildingTime(197);
        $nB->setWaterCost(63);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(100);        $nB->setFuelCost(19);
        $nB->setBuildingRequirement(8);        $nB->setTechnologyRequirement(64);
        $nB->setHp(340);    $nB->setAttack(26); $nB->setArmor(22); $nB->setType(3); $nB->setSpeed(50);
        $em->persist($nB);
        $desc = new \EIP\HRBundle\Entity\HRUnitDescription($nB);
        $desc->setContent("humvee.desc");
        $em->persist($desc);

        $nB = new \EIP\HRBundle\Entity\HRUnitSchema();
        $nB->setName("tank");        $nB->setImg(5);    $nB->setBuildingTime(251);
        $nB->setWaterCost(190);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(430);        $nB->setFuelCost(54);
        $nB->setBuildingRequirement(200);        $nB->setTechnologyRequirement(64);
        $nB->setHp(1240);    $nB->setAttack(180); $nB->setArmor(50); $nB->setType(3); $nB->setSpeed(30);
        $em->persist($nB);
        $desc = new \EIP\HRBundle\Entity\HRUnitDescription($nB);
        $desc->setContent("tank.desc");
        $em->persist($desc);

        $em->flush();
        $output->writeln("Creating: Units Schema - Done.");
    }

    private function technologySchema(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln("Creating: Technologies Schema - Processing.");

        // TYPE 0 - TECHNOLOGIES FONDAMENTALES
        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.build1");    $nB->setType(0);    $nB->setRValue(1);
        $nB->setBuildingTime(36);
        $nB->setWaterCost(50);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(50);        $nB->setFuelCost(10);
        $nB->setBuildingRequirement(0);        $nB->setTechnologyRequirement(0);
                $nB->setDescription("techno.build1.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.build2");    $nB->setType(0);    $nB->setRValue(2);
        $nB->setBuildingTime(176);
        $nB->setWaterCost(100);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(100);        $nB->setFuelCost(20);
        $nB->setBuildingRequirement(0);        $nB->setTechnologyRequirement(1);
                $nB->setDescription("techno.build2.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.build3");    $nB->setType(0);    $nB->setRValue(4);
        $nB->setBuildingTime(314);
        $nB->setWaterCost(500);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(500);        $nB->setFuelCost(100);
        $nB->setBuildingRequirement(0);        $nB->setTechnologyRequirement(2);
                $nB->setDescription("techno.build3.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.build4");    $nB->setType(0);    $nB->setRValue(8);
        $nB->setBuildingTime(649);
        $nB->setWaterCost(1500);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(1500);        $nB->setFuelCost(500);
        $nB->setBuildingRequirement(0);        $nB->setTechnologyRequirement(4);
                $nB->setDescription("techno.build4.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        // TYPE 1 - TECHNOLOGIES AVANCÉES
        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.advanced1");    $nB->setType(1);    $nB->setRValue(1024);
        $nB->setBuildingTime(754);
        $nB->setWaterCost(5000);            $nB->setPureWaterCost(0);
        $nB->setSteelCost(5000);            $nB->setFuelCost(1000);
        $nB->setBuildingRequirement(64);    $nB->setTechnologyRequirement(0);
                $nB->setDescription("techno.advanced1.desc");
        $em->persist($nB);

        // TYPE 2 - TECHNOLOGIES DE COMBAT
        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.weapon1");    $nB->setType(2);    $nB->setRValue(16);
        $nB->setBuildingTime(36);
        $nB->setWaterCost(50);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(50);        $nB->setFuelCost(10);
        $nB->setBuildingRequirement(0);        $nB->setTechnologyRequirement(0);
                $nB->setDescription("techno.weapon1.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.weapon2");    $nB->setType(2);    $nB->setRValue(32);
        $nB->setBuildingTime(176);
        $nB->setWaterCost(100);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(100);        $nB->setFuelCost(20);
        $nB->setBuildingRequirement(16);        $nB->setTechnologyRequirement(16);
                $nB->setDescription("techno.weapon2.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.weapon3");    $nB->setType(2);    $nB->setRValue(64);
        $nB->setBuildingTime(314);
        $nB->setWaterCost(500);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(500);        $nB->setFuelCost(100);
        $nB->setBuildingRequirement(32);        $nB->setTechnologyRequirement(32);
                $nB->setDescription("techno.weapon3.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        // TYPE 3 - TECHNOLOGIES DE TRANSPORT
        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.training1");    $nB->setType(3);    $nB->setRValue(128);
        $nB->setBuildingTime(36);
        $nB->setWaterCost(50);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(50);        $nB->setFuelCost(10);
        $nB->setBuildingRequirement(16);        $nB->setTechnologyRequirement(0);
                $nB->setDescription("techno.training1.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.training2");    $nB->setType(3);    $nB->setRValue(256);
        $nB->setBuildingTime(176);
        $nB->setWaterCost(100);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(100);        $nB->setFuelCost(20);
        $nB->setBuildingRequirement(16);        $nB->setTechnologyRequirement(128);
                $nB->setDescription("techno.training2.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $nB->setName("techno.training3");    $nB->setType(3);    $nB->setRValue(512);
        $nB->setBuildingTime(314);
        $nB->setWaterCost(500);        $nB->setPureWaterCost(0);
        $nB->setSteelCost(500);        $nB->setFuelCost(100);
        $nB->setBuildingRequirement(16);        $nB->setTechnologyRequirement(256);
                $nB->setDescription("techno.training3.desc");
                $associatedBuff = $em->createQuery('SELECT bs FROM EIPHRBundle:HRBuffSchema bs WHERE bs.name = :buffname')
                        ->setParameter(':buffname', 'buff.'.$nB->getName())->getSingleResult();
                $nB->setBuffSchema($associatedBuff);
        $em->persist($nB);

        $em->flush();
        $output->writeln("Creating: Technologies Schema - Done.");
    }

    private function heroSchema(OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();

        $output->writeln("Creating: Heroes Schema - Processing.");

        $nB = new \EIP\HRBundle\Entity\HRHeroSchema();
        $nB->setName("general");
        $nB->setImg(1);
        $nB->setBonusAttack(0.01);
        $nB->setBonusArmor(0);
        $nB->setBonusSpeed(0.01);
        $nB->setBonusHealth(0);
        $nB->setBonusCollectRate(7);
        $nB->setBonusAttackPerLevel(0.02);
        $nB->setBonusArmorPerLevel(0.01);
        $nB->setBonusSpeedPerLevel(0.01);
        $nB->setBonusHealthPerLevel(0.01);
        $nB->setBonusCollectRatePerLevel(1);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRHeroSchema();
        $nB->setName("protector");
        $nB->setImg(2);
        $nB->setBonusAttack(0);
        $nB->setBonusArmor(0.01);
        $nB->setBonusSpeed(0);
        $nB->setBonusHealth(0.01);
        $nB->setBonusCollectRate(5);
        $nB->setBonusAttackPerLevel(0.01);
        $nB->setBonusArmorPerLevel(0.03);
        $nB->setBonusSpeedPerLevel(0.01);
        $nB->setBonusHealthPerLevel(0.01);
        $nB->setBonusCollectRatePerLevel(1);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRHeroSchema();
        $nB->setName("warlord");
        $nB->setImg(3);
        $nB->setBonusAttack(0.01);
        $nB->setBonusArmor(0.01);
        $nB->setBonusSpeed(0);
        $nB->setBonusHealth(0);
        $nB->setBonusCollectRate(7);
        $nB->setBonusAttackPerLevel(0.04);
        $nB->setBonusArmorPerLevel(0.01);
        $nB->setBonusSpeedPerLevel(0.01);
        $nB->setBonusHealthPerLevel(0.01);
        $nB->setBonusCollectRatePerLevel(1);
        $em->persist($nB);

        $nB = new \EIP\HRBundle\Entity\HRHeroSchema();
        $nB->setName("scientist");
        $nB->setImg(4);
        $nB->setBonusAttack(0);
        $nB->setBonusArmor(0);
        $nB->setBonusSpeed(0.01);
        $nB->setBonusHealth(0);
        $nB->setBonusCollectRate(15);
        $nB->setBonusAttackPerLevel(0.02);
        $nB->setBonusArmorPerLevel(0.01);
        $nB->setBonusSpeedPerLevel(0.01);
        $nB->setBonusHealthPerLevel(0.01);
        $nB->setBonusCollectRatePerLevel(2);
        $em->persist($nB);

        $em->flush();
        $output->writeln("Creating: Heroes Schema - Done.");
        }

        private function buffAndItemSchema($output) {
            $em = $this->getContainer()->get('doctrine')->getManager();
             // adding Buff schemas
            $buffschema = new \EIP\HRBundle\Entity\HRBuffSchema();
            $buffschema->setDuration(90*60);
            $buffschema->setName('buff.vinyl');
            $buffschema->setType(\EIP\HRBundle\Entity\HRBuffSchema::RESOURCES_ALL_TYPE);
            $buffschema->setValue(10);
            $em->persist($buffschema);
            $vinylBuff = $buffschema;

            $buffschema = new \EIP\HRBundle\Entity\HRBuffSchema();
            $buffschema->setDuration(90*60);
            $buffschema->setName('buff.attack');
            $buffschema->setType(\EIP\HRBundle\Entity\HRBuffSchema::ATTACK_ALL_TYPE);
            $buffschema->setValue(0.2);
            $em->persist($buffschema);

            $buffschema = new \EIP\HRBundle\Entity\HRBuffSchema();
            $buffschema->setDuration(90*60);
            $buffschema->setName('buff.armor');
            $buffschema->setType(\EIP\HRBundle\Entity\HRBuffSchema::ARMOR_ALL_TYPE);
            $buffschema->setValue(0.4);
            $em->persist($buffschema);

            // technobuffs
            // weapons
            foreach (array(
                'buff.techno.weapon1' => 0.1,
                'buff.techno.weapon2' => 0.1,
                'buff.techno.weapon3' => 0.2
            ) as $buffname => $value) {
                $technobuff = new \EIP\HRBundle\Entity\HRBuffSchema();
                $technobuff->setDuration(0); $technobuff->setPermanent(true);
                $technobuff->setType(\EIP\HRBundle\Entity\HRBuffSchema::ATTACK_ALL_TYPE);
                $technobuff->setName($buffname); $technobuff->setValue($value);
                $em->persist($technobuff);
            }
            // building
            foreach (array(
                'buff.techno.build1' => 5,
                'buff.techno.build2' => 5,
                'buff.techno.build3' => 5,
                'buff.techno.build4' => 5
            ) as $buffname => $value) {
                $technobuff = new \EIP\HRBundle\Entity\HRBuffSchema();
                $technobuff->setDuration(0); $technobuff->setPermanent(true);
                $technobuff->setType(\EIP\HRBundle\Entity\HRBuffSchema::BUILDING_TIME_TYPE);
                $technobuff->setName($buffname); $technobuff->setValue($value);
                $em->persist($technobuff);
            }
            // training
            foreach (array(
                'buff.techno.training1' => 5,
                'buff.techno.training2' => 5,
                'buff.techno.training3' => 5
            ) as $buffname => $value) {
                $technobuff = new \EIP\HRBundle\Entity\HRBuffSchema();
                $technobuff->setDuration(0); $technobuff->setPermanent(true);
                $technobuff->setType(\EIP\HRBundle\Entity\HRBuffSchema::TRAINING_TIME_TYPE);
                $technobuff->setName($buffname); $technobuff->setValue($value);
                $em->persist($technobuff);
            }

            // adding item schemas
            //1
            $itemschema1 = new \EIP\HRBundle\Entity\HRItemSchema('itemWaterResource1', '1.png');
            $itemschema1->setResourceName('water');
            $itemschema1->setValue(100);
            $itemschema1->setDescription('itemWaterResource1.desc');
            $em->persist($itemschema1);
            //2
            $uschema = $em->createQuery("SELECT s FROM EIPHRBundle:HRUnitSchema s WHERE LOWER(s.name) = :tank")->setParameter(':tank', 'tank')->getSingleResult();
            $itemschema2 = new \EIP\HRBundle\Entity\HRItemSchema('itemTankUnit1', '3.png');
            $itemschema2->setUnitSchema($uschema);
            $itemschema2->setValue(10);
            $itemschema2->setDescription('itemTankUnit1.desc');
            $em->persist($itemschema2);
            //3
            $itemschema3 = new \EIP\HRBundle\Entity\HRItemSchema('itemResourcesBuff1', '2.png');
            $itemschema3->setBuffSchema($vinylBuff);
            $itemschema3->setDescription('itemResourcesBuff1.desc');
            $em->persist($itemschema3);

            $em->flush();
            $output->writeln("Creating: Buffs and items Schema - Done.");
        }

        private function questSchema(OutputInterface $output)
        {
            $em = $this->getContainer()->get('doctrine')->getManager();

            $itemSchemas = $em->getRepository('EIPHRBundle:HRItemSchema')->findAll();
            $unitSchemas = $em->getRepository('EIPHRBundle:HRunitSchema')->findAll();

            $qs = new \EIP\HRBundle\Entity\HRQuestSchema();
            $qs->setName("resources.q1");
            $qs->setDescription("one.desc");
            $qs->setXpReward(1200);
            foreach (range(0,2) as $i)
                $qs->getItemReward()->add($itemSchemas[$i]);
            $qs->setType(\EIP\HRBundle\Entity\HRQuestSchema::GIVE_RESOURCES);
            $qs->setData(array('water'=>250,'pureWater'=>400,'fuel'=>600,'steel'=>200));
            $qs->setOnce(false);
            $em->persist($qs);

            $qs = new \EIP\HRBundle\Entity\HRQuestSchema();
            $qs->setName("destroy.q1");
            $qs->setDescription("two.desc");
            $qs->setXpReward(1650);
            $qs->setType(\EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT);
            $qs->setOnce(false);
            $qs->setData(array($unitSchemas[0]->getId() => 3, $unitSchemas[1]->getId() => 5));
            $em->persist($qs);

            $em->flush();
            $output->writeln("Creating: Quests - Done.");
        }

        private function achievementSchema(OutputInterface $output) {
            $em = $this->getContainer()->get('doctrine')->getManager();

            $typeValues = array(
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_GAMES => array(1, 10, 100),
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_UNITS_RECRUITED => array(1, 100, 1000),
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_BUILDINGS  => array(1, 100, 1000),
                \EIP\HRBundle\Entity\HRAchievementSchema::HERO_MAX_LEVEL  => array(2, 5, 10),
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_QUEST_COMPLETED => array(1, 100, 1000),
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_UNIT_DESTROYED  => array(1, 100, 1000),
            );
             $typeNames = array(
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_GAMES => 'game.join',
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_UNITS_RECRUITED => 'units.recruited',
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_BUILDINGS  => 'buildings',
                \EIP\HRBundle\Entity\HRAchievementSchema::HERO_MAX_LEVEL  => 'max.hero.level',
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_QUEST_COMPLETED => 'quests.completed',
                \EIP\HRBundle\Entity\HRAchievementSchema::NB_UNIT_DESTROYED  => 'units.destroyed',
            );

            foreach ($typeValues as $key => $valuesArray)
            {
                $acs1 = new \EIP\HRBundle\Entity\HRAchievementSchema();
                $acs3 = new \EIP\HRBundle\Entity\HRAchievementSchema();
                $acs2 = new \EIP\HRBundle\Entity\HRAchievementSchema();

                $acs1->setName($typeNames[$key].'1');
                $acs2->setName($typeNames[$key].'2');
                $acs3->setName($typeNames[$key].'3');
                foreach (array($acs1, $acs2, $acs3) as $a)
                    $a->setType($key);
                $acs1->setValue($valuesArray[0]);
                $acs2->setValue($valuesArray[1]);
                $acs3->setValue($valuesArray[2]);
                $acs1->setStep(0);
                $acs2->setStep(1);
                $acs3->setStep(2);

                $acs1->setPrev(null);
                $acs1->setNext($acs2);
                $acs2->setPrev($acs1);
                $acs2->setNext($acs3);
                $acs3->setPrev($acs2);
                $acs3->setNext(null);
                foreach (array($acs1, $acs2, $acs3) as $a)
                    $em->persist($a);
            }

            $em->flush();
            $output->writeln("Creating: Achievements - Done.");
        }

        private function createNeutralUser($output) {
            $em = $this->getContainer()->get('doctrine')->getManager();
            $pw = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);
            $nUser = TestTools::getValidUser('HumanReborn', $pw, 'hradmin@gmail.com', $this->getContainer()->get('security.encoder_factory'));
            $em->persist($nUser);
            $em->flush();
            $output->writeln("Neutral user 'HumanReborn' created");
        }


}
