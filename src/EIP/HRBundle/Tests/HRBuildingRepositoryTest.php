<?php

use EIP\HRBundle\Utils\TestTools;

class HRBuildingRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{
    public function testFetchTownBuildingsMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $buildingSchema1 = TestTools::getValidBuildingSchema('testschema1');
        $buildingSchema2 = TestTools::getValidBuildingSchema('testschema2');
        $buildingSchema3 = TestTools::getValidBuildingSchema('testschema3');
        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
        $town2 = TestTools::getValidTown('testtown2', $game1, $user1);

        $b1 = TestTools::getValidBuilding($buildingSchema1, $town1);
        $b2 = TestTools::getValidBuilding($buildingSchema2, $town1);
        $b3 = TestTools::getValidBuilding($buildingSchema3, $town2);

        $arr = array($user1, $game1, $town1, $town2,
            $buildingSchema1, $buildingSchema2, $buildingSchema3, $b1, $b2, $b3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $buildings = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->fetchTownBuildings($town1);
        $this->assertCount(2, $buildings);
        $this->assertEquals($buildings[0]->getSchema()->getName(), 'testschema1');
        $this->assertEquals($buildings[1]->getSchema()->getName(), 'testschema2');

        $buildings = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->fetchTownBuildings($town2);
        $this->assertCount(1, $buildings);
        $this->assertEquals($buildings[0]->getSchema()->getName(), 'testschema3');

        $em->remove($b3);
        $em->flush();

        $buildings = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->fetchTownBuildings($town2);
        $this->assertCount(0, $buildings);

    }

    public function testGetBuildingScoreMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $buildingSchema1 = TestTools::getValidBuildingSchema('testschema1');
        $buildingSchema1->setRValue(1);

        $buildingSchema2 = TestTools::getValidBuildingSchema('testschema2');
        $buildingSchema2->setRValue(2);

        $buildingSchema3 = TestTools::getValidBuildingSchema('testschema3');
        $buildingSchema3->setRValue(4);

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
        $town2 = TestTools::getValidTown('testtown2', $game1, $user1);

        $b1 = TestTools::getValidBuilding($buildingSchema1, $town1);
        $b2 = TestTools::getValidBuilding($buildingSchema2, $town1);
        $b3 = TestTools::getValidBuilding($buildingSchema3, $town2);

        $arr = array($user1, $game1, $town1, $town2,
            $buildingSchema1, $buildingSchema2, $buildingSchema3, $b1, $b2, $b3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $buildingScore = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town1);
        $this->assertEquals(3, $buildingScore);

        $buildingScore = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town2);
        $this->assertEquals(4, $buildingScore);

        $b4 = TestTools::getValidBuilding($buildingSchema1, $town1);
        $em->persist($b4);
        $em->flush();
        // $buildingScore shouldn't change if building is built twice
        $buildingScore = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town1);
        $this->assertEquals(3, $buildingScore);

    }
}