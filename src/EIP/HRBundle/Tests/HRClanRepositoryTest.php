<?php

use EIP\HRBundle\Utils\TestTools;
use EIP\HRBundle\Utils\ATestCase;

class HRClanRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{
    public function testGetClansListMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'validpwd', 'validmail3@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $game2 = TestTools::getValidGame('testgame2');
        $arr = array($user1, $user2, $user3, $game1, $game2);
        foreach ($arr as $e)
            $em->persist($e);
        $em->flush();
        $clan1 = TestTools::getValidClan('testClan1', $game1);
        $clan2 = TestTools::getValidClan('testClan2', $game2);
        $clan3 = TestTools::getValidClan('testClan3', $game2);
        $arr = array($clan1, $clan2, $clan3);
        foreach ($arr as $e)
            $em->persist($e);
        $em->flush();
//        $clans = $em->getRepository('EIPHRBundle:HRClan')->getClansList();
//        $this->assertCount(3, $clans);
//        $this->assertEquals($clan1, $clans[0]);
//        $this->assertEquals($clan2, $clans[1]);
//        $this->assertEquals($clan3, $clans[2]);
//        $this->assertEquals($clans[0]->getPublicPresentation(), 'public');
//        $this->assertEquals($clans[0]->getPrivatePresentation(), 'private');
//        $this->assertEquals($clans[0]->getIdGame() , $game1->getId());
//        $this->assertEquals($clans[1]->getIdGame() , $game2->getId());
//        $this->assertEquals($clans[2]->getIdGame() , $game2->getId());
    }
}