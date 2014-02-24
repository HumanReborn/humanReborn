<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;


class HRBuildingSchemaTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testCreation()
    {
        $schema = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $arr = array(
            $schema->getBuildingRequirement(),
            $schema->getName(),
            $schema->getRValue(),
            $schema->getTechnologyRequirement(),
            );
        foreach ($arr as $r)
        {
            $this->assertNull($r);
        }

        $arr = array(
            $schema->getFuelCollectRate(),
            $schema->getFuelCost(),
            $schema->getPureWaterCollectRate(),
            $schema->getPureWaterCost(),
            $schema->getSteelCollectRate(),
            $schema->getSteelCost(),
            $schema->getWaterCollectRate(),
            $schema->getWaterCost(),
            $schema->getBuildingTime(),
        );
        foreach ($arr as $r)
        {
            $this->assertEquals($r, 0);
        }

    }

    public function testPersist()
    {
        $em = self::$doctrine->getManager();
        // wipe the town table content and insert new user
        TestTools::clearTables($em);

        $schema = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $schema->setBuildingRequirement(0);
        $schema->setTechnologyRequirement(0);
        $schema->setRValue(0);
        $schema->setName("MyTestSchema");
        $em->persist($schema);
        $em->flush();
        //
        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRBuildingSchema')->findOneBy(array('name' => 'MyTestSchema'));
        $this->assertNotNull($fetchedEntity);
    }

    public function testEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $schema = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $schema->setBuildingRequirement(0);
        $schema->setTechnologyRequirement(0);
        $schema->setRValue(0);
        $schema->setName("MyTestSchema");
        $em->persist($schema);
        $em->flush();

        $toEdit = self::$doctrine->getRepository('EIPHRBundle:HRBuildingSchema')->findOneBy(array('name' => 'MyTestSchema'));
        $this->assertNotNull($toEdit);
        $toEdit->setName('EditedName');
        $em->flush();
        $check = self::$doctrine->getRepository('EIPHRBundle:HRBuildingSchema')->findOneBy(array('name' => 'EditedName'));
        $this->assertNotNull($check);
    }

}