<?php

use EIP\HRBundle\Utils\TestTools;

class HRBuildingQueueRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{

    public function testFetchTownQueuedBuildingsMethod()
    {
        // insert 3 armies to different players and get them
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $buildingSchema1 = TestTools::getValidBuildingSchema('testschema1');
        $buildingSchema2 = TestTools::getValidBuildingSchema('testschema2');
        $buildingSchema3 = TestTools::getValidBuildingSchema('testschema3');

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');

        $game1 = TestTools::getValidGame('testgame1');

        $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
        $town2 = TestTools::getValidTown('testtown2', $game1, $user1);
        $town3 = TestTools::getValidTown('testtown3', $game1, $user1);


        $q1 = TestTools::getValidBuildingQueue($town1, $buildingSchema1, 1);
        $q2 = TestTools::getValidBuildingQueue($town2, $buildingSchema2, 2);
        $q3 = TestTools::getValidBuildingQueue($town2, $buildingSchema3, 3);

        $arr = array($user1, $game1, $town1, $town2, $town3,
            $buildingSchema1, $buildingSchema2, $buildingSchema3, $q1, $q2, $q3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();
        // expected 1 for town 1,
        //  2 for town 2
        //  and 0 for town 3
        $queues = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchTownQueuedBuildings($town1);
        $this->assertCount(1, $queues);

        $queues = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchTownQueuedBuildings($town2);
        $this->assertCount(2, $queues);

        $queues = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchTownQueuedBuildings($town3);
        $this->assertCount(0, $queues);

    }

    public function testFetchLastBuildingCompletionTimeMethod()
    {
        // insert 3 armies to different players and get them
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $buildingSchema1 = TestTools::getValidBuildingSchema('testschema1');
        $buildingSchema1->setBuildingTime(15);

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
        $town2 = TestTools::getValidTown('testtown2', $game1, $user1);

        $arr = array($user1, $game1, $town1, $town2,
            $buildingSchema1);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();
        // no building in queue, completionTime return = time()
        $ctime = time();
        $startTime = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchLastBuildingCompletionTime($town1->getId());
        $this->assertEquals($ctime, $startTime);
        // inserting one building in the queue
        $q1 = TestTools::getValidBuildingQueue($town1, $buildingSchema1, $startTime);
        $em->persist($q1);
        $em->flush();
        // rerun test
        $expectedTime = $ctime + $buildingSchema1->getBuildingTime();
        $startTime = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchLastBuildingCompletionTime($town1->getId());
        $this->assertEquals($expectedTime, $startTime);
        // check completion time in town2 -- ctime expected
        $startTime = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchLastBuildingCompletionTime($town2->getId());
        $this->assertEquals(time(), $startTime);
    }

    public function testFetchUserQueueMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $buildingSchema1 = TestTools::getValidBuildingSchema('testschema1');
        $buildingSchema2 = TestTools::getValidBuildingSchema('testschema2');
        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'validpwd2', 'validmail2@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $game2 = TestTools::getValidGame('testgame2');
        $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
        $town2 = TestTools::getValidTown('testtown2', $game1, $user1);

        // insert 3 elements in the user building queue
        $ctime = time();
        $q1 = TestTools::getValidBuildingQueue($town1, $buildingSchema1, $ctime);
        $q2 = TestTools::getValidBuildingQueue($town1, $buildingSchema2, $q1->getEndTime());
        $q3 = TestTools::getValidBuildingQueue($town2, $buildingSchema1, $q2->getEndTime());

        $arr = array($user1, $user2, $game1, $town1, $town2,
            $buildingSchema1, $buildingSchema2, $q1, $q2, $q3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();


        // get user1 queue
        $bq = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchUserQueue($user1->getId(), $game1->getId());
        $this->assertCount(3, $bq);
        $this->assertEquals($bq[0]->getEndTime(), $q1->getEndTime());
        $this->assertEquals($bq[1]->getEndTime(), $q2->getEndTime());
        $this->assertEquals($bq[2]->getEndTime(), $q3->getEndTime());
        // user 2
        $bq = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchUserQueue($user2->getId(), $game1->getId());
        $this->assertCount(0, $bq);
        // user1, other game
        $bq = self::$doctrine->getRepository('EIPHRBundle:HRBuildingQueue')->fetchUserQueue($user1->getId(), $game2->getId());
        $this->assertCount(0, $bq);
    }

}