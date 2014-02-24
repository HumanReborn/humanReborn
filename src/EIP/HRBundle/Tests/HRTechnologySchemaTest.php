<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;


class HRTechnologySchemaTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testCreation()
    {
        $schema = new \EIP\HRBundle\Entity\HRTechnologySchema();
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
            $schema->getFuelCost(),
            $schema->getPureWaterCost(),
            $schema->getSteelCost(),
            $schema->getWaterCost(),
            $schema->getBuildingTime()
        );
        foreach ($arr as $r)
        {
            $this->assertEquals($r, 0);
        }
    }

    public function testPersistAndEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $schema = TestTools::getValidTechnologySchema('MyTestSchema');
        $em->persist($schema);
        $em->flush();
        //
        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRTechnologySchema')->findOneBy(array('name' => 'MyTestSchema'));
        $this->assertNotNull($fetchedEntity);
        // edit
        $fetchedEntity->setName('NewTestSchemaName');
        $em->flush();
        $check = self::$doctrine->getRepository('EIPHRBundle:HRTechnologySchema')->findOneBy(array('name' => 'NewTestSchemaName'));
        $this->assertNotNull($check);
    }



}