<?php

use EIP\HRBundle\Utils\TestTools;

class HRArmyRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{
     public function testFetchAllArmiesMethod() {
         // insert 3 armies to different players and get them
         $em = self::$doctrine->getManager();
         TestTools::clearTables($em);

         $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
         $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');
         $user3 = TestTools::getValidUser('testuser3', 'validpwd', 'validmail3@mail.com');

         $game1 = TestTools::getValidGame('testgame1');
         $game2 = TestTools::getValidGame('testgame2');
         $game3 = TestTools::getValidGame('testgame3');

         $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
         $town2 = TestTools::getValidTown('testtown2', $game2, $user2);
         $town3 = TestTools::getValidTown('testtown3', $game3, $user3);


         $army1 = TestTools::getValidArmy($town1);
         $army2 = TestTools::getValidArmy($town2);
         $army3 = TestTools::getValidArmy($town3);

         $arr = array($user1, $user2, $user3, $game1, $game2, $game3, $town1, $town2, $town3,
             $army1, $army2, $army3);
         foreach ($arr as $e)
         {
             $em->persist($e);
         }

         $em->flush();

         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchAllArmies();
         $this->assertCount(3, $armies);
     }

     public function testFetchPlayerArmiesMethod()
     {
         $em = self::$doctrine->getManager();
         TestTools::clearTables($em);

         $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
         $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');

         $game1 = TestTools::getValidGame('testgame1');
         $game2 = TestTools::getValidGame('testgame2');
         $game3 = TestTools::getValidGame('testgame3');

         $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
         $town2 = TestTools::getValidTown('testtown2', $game1, $user1);
         $town3 = TestTools::getValidTown('testtown3', $game2, $user2);
         $town4 = TestTools::getValidTown('testtown4', $game3, $user1);

         $army1 = TestTools::getValidArmy($town1);
         $army2 = TestTools::getValidArmy($town2);
         $army3 = TestTools::getValidArmy($town3);
         $army4 = TestTools::getValidArmy($town4);

         $arr = array($user1, $user2, $game1, $game2, $game3, $town1, $town2, $town3, $town4,
             $army1, $army2, $army3, $army4);
         foreach ($arr as $e)
         {
             $em->persist($e);
         }
         $em->flush();
         // user1
         // two armies in game 1
         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerArmies($user1->getId(), $game1->getId());
         $this->assertCount(2, $armies);
         // none in game 2
         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerArmies($user1->getId(), $game2->getId());
         $this->assertCount(0, $armies);
         // one army in game 3
         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerArmies($user1->getId(), $game3->getId());
         $this->assertCount(1, $armies);
         // user 2
         // only one army in game2
         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerArmies($user2->getId(), $game1->getId());
         $this->assertCount(0, $armies);
         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerArmies($user2->getId(), $game2->getId());
         $this->assertCount(1, $armies);
         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerArmies($user2->getId(), $game3->getId());
         $this->assertCount(0, $armies);
     }

     public function testFetchPlayerGarrisonsMethod()
     {
         $em = self::$doctrine->getManager();
         TestTools::clearTables($em);

         $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail@mail.com');
         $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');

         $game1 = TestTools::getValidGame('testgame1');
         $game2 = TestTools::getValidGame('testgame2');
         $game3 = TestTools::getValidGame('testgame3');

         $town1 = TestTools::getValidTown('testtown1', $game1, $user1);
         $town2 = TestTools::getValidTown('testtown2', $game1, $user1);
         $town3 = TestTools::getValidTown('testtown3', $game2, $user2);
         $town4 = TestTools::getValidTown('testtown4', $game3, $user1);

         $army1 = TestTools::getValidArmy($town1);
         $army2 = TestTools::getValidArmy($town2);
         $army3 = TestTools::getValidArmy($town3);
         $army4 = TestTools::getValidArmy($town4);

         $army1->setGarrison(true);
         $army2->setGarrison(true);

         $arr = array($user1, $user2, $game1, $game2, $game3, $town1, $town2, $town3, $town4,
             $army1, $army2, $army3, $army4);
         foreach ($arr as $e)
         {
             $em->persist($e);
         }
         $em->flush();

         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerGarrisons($user1->getId(), $game1->getId());
         $this->assertCount(2, $armies);

         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerGarrisons($user1->getId(), $game2->getId());
         $this->assertCount(0, $armies);

         $armies = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->fetchPlayerGarrisons($user1->getId(), $game3->getId());
         $this->assertCount(0, $armies);
     }


}