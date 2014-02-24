<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRTownRepositoryTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testFetchTownsMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'bob42', 'bobmail1@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'bob42', 'bobmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'bob42', 'bobmail3@mail.com');
        $game1 = TestTools::getValidGame('testGame1');
        $game2 = TestTools::getValidGame('testGame2');
        $game3 = TestTools::getValidGame('testGame3');
        $town1 = TestTools::getValidTown('testTown1', $game1, $user1);
        $town2 = TestTools::getValidTown('testTown2', $game2, $user1);
        $town3 = TestTools::getValidTown('testTown3', $game3, $user2);

        $arr = array($user1, $user2, $user3, $game1, $game2, $game3, $town1, $town2, $town3);

        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRTown')->fetchTowns();
        $this->assertCount(3, $result1);

        $em->remove($town2);
        $em->flush();

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRTown')->fetchTowns();
        $this->assertCount(2, $result2);
    }

}