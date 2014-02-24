<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRGameLinkTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testPersistAndEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('testUser', 'testPassword', 'testmail@testmail.com');
        $user2 = TestTools::getValidUser('testUser2', 'testPassword2', 'testmail2@testmail.com');
        $game = TestTools::getValidGame('testGame');
        $gl = TestTools::getValidGameLink($user, $game);
        $this->assertNotNull($gl->getJoinedOn());

        foreach(array($user, $user2, $game, $gl) as $e)
        {
            $em->persist($e);
        }
        $em->flush();
        // test persist
        $checkPersist = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
            'game' => $game,
            'user' => $user,
        ));
        $this->assertNotNull($checkPersist);
        // test edit
        $checkPersist->setUser($user2);
        $em->flush();
        $checkEdit = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
            'game' => $game,
            'user' => $user2,
        ));
        $this->assertNotNull($checkEdit);
    }

    public function testCanBuyMethod()
    {
        // creating a gamelink
        $user = TestTools::getValidUser('testUser', 'testPassword', 'testmail@testmail.com');
        $game = TestTools::getValidGame('testGame');
        $gl = TestTools::getValidGameLink($user, $game);
        $resources = $gl->getResources();
        foreach ($resources as $r)
        {
            $this->assertEquals($r, 0);
        }

        // creating a schema to buy
        $schema = TestTools::getValidBuildingSchema('testSchema');
        $schema->setWaterCost(10); // can't buy this schema
        $this->assertFalse($gl->canBuy($schema));

        // set new resources
        $resources['steel'] = 20;
        $gl->setResources($resources);

        // retest, failure expected
        $this->assertFalse($gl->canBuy($schema));

        // set new resources
        $resources['water'] = 20;
        $gl->setResources($resources);

        // retest, success expected
        $this->assertTrue($gl->canBuy($schema));
    }

    public function testBuyMethod()
    {
        // creating a gamelink and set all resources to 50
        $user = TestTools::getValidUser('testUser', 'testPassword', 'testmail@testmail.com');
        $game = TestTools::getValidGame('testGame');
        $gl = TestTools::getValidGameLink($user, $game);
        $resources = $gl->getResources();
        foreach ($resources as $r)
        {
            $this->assertEquals($r, 0);
        }
        foreach (array('water','pureWater','steel','fuel') as $key)
        {
            $resources[$key] = 50;
        }
        $gl->setResources($resources);

        // creating a schema to buy
        $schema = TestTools::getValidBuildingSchema('testSchema');
        $schema->setWaterCost(10);
        $schema->setPureWaterCost(20);
        $schema->setSteelCost(30);
        $schema->setFuelCost(40);

        $this->assertTrue($gl->canBuy($schema));
        $gl->Buy($schema);

        $newResources = $gl->getResources();
        $this->assertEquals($newResources['fuel'], 10);
        $this->assertEquals($newResources['steel'], 20);
        $this->assertEquals($newResources['pureWater'], 30);
        $this->assertEquals($newResources['water'], 40);
    }

    public function testAddCollectingBuildingMethod()
    {
        // creating a gamelink
        $user = TestTools::getValidUser('testUser', 'testPassword', 'testmail@testmail.com');
        $game = TestTools::getValidGame('testGame');
        $gl = TestTools::getValidGameLink($user, $game);
        $resources = $gl->getResources();

        $this->assertEquals($resources['waterGain'], 0);
        $this->assertEquals($resources['pureWaterGain'], 0);
        $this->assertEquals($resources['steelGain'], 0);
        $this->assertEquals($resources['fuelGain'], 0);

          // creating a collecting schema to buy
        $schema = TestTools::getValidBuildingSchema('testSchema');
        $schema->setWaterCollectRate(10);
        $schema->setPureWaterCollectRate(20);
        $schema->setSteelCollectRate(30);
        $schema->setFuelCollectRate(40);


        $gl->addCollectingBuilding($schema);

        $resources = $gl->getResources();
        $this->assertEquals($resources['waterGain'], 10);
        $this->assertEquals($resources['pureWaterGain'], 20);
        $this->assertEquals($resources['steelGain'], 30);
        $this->assertEquals($resources['fuelGain'], 40);

    }

}