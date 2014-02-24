<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRUnitQueueRepositoryRepositoryTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testFetchLastUnitCompletionTimeMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('testuser', 'password', 'testmail@mail.com');
        $game = TestTools::getValidGame('testgame');
        $town = TestTools::getValidTown('testtown', $game, $user);
        $unitSchema = TestTools::getValidUnitSchema('testschema', 15);
        $army = TestTools::getValidArmy($town);
        $army->setGarrison(true);

        $arr = array($user, $game, $town, $army, $unitSchema);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $ctime = time();
        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRUnitQueue')->fetchLastUnitCompletionTIme($town);
        $this->assertEquals($ctime, $result1);

        //
        $uc = TestTools::getValidUnitQueue($unitSchema, $army, $ctime);

        $em->persist($uc);
        $em->flush();

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRUnitQueue')->fetchLastUnitCompletionTIme($town);
        $this->assertEquals($ctime + $unitSchema->getBuildingTime(), $result2);
    }

    public function testFetchUserQueueMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('testuser', 'password', 'testmail@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $game2 = TestTools::getValidGame('testgame2');
        $town = TestTools::getValidTown('testtown', $game1, $user);
        $unitSchema1 = TestTools::getValidUnitSchema('testschema1', 15);
        $unitSchema2 = TestTools::getValidUnitSchema('testschema2', 30);
        $army = TestTools::getValidArmy($town);
        $army->setGarrison(true);

        $ctime = time();
        $uc1 = TestTools::getValidUnitQueue($unitSchema1, $army, $ctime);
        $uc2 = TestTools::getValidUnitQueue($unitSchema2, $army, $uc1->getEndTime());
        $uc3 = TestTools::getValidUnitQueue($unitSchema1, $army, $uc2->getEndTime());

        $arr = array($user, $game1, $game2, $town, $army, $unitSchema1, $unitSchema2, $uc1, $uc2, $uc3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRUnitQueue')->fetchUserQueue($user, $game1);
        $this->assertCount(3, $result1);
        $this->assertEquals($result1[0], $uc1);
        $this->assertEquals($result1[1], $uc2);
        $this->assertEquals($result1[2], $uc3);

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRUnitQueue')->fetchUserQueue($user, $game2);
        $this->assertCount(0, $result2);

    }

}



