<?php

use EIP\HRBundle\Utils\TestTools;

class HRGameLinkRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{
    public function testGetUserGameLinksMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail1@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'validpwd', 'validmail3@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $game2 = TestTools::getValidGame('testgame2');

        $gl1 = TestTools::getValidGameLink($user1, $game1);
        $gl2 = TestTools::getValidGameLink($user1, $game2);
        $gl3 = TestTools::getValidGameLink($user2, $game2);

        $arr = array($user1, $user2, $user3, $game1, $game2, $gl1, $gl2, $gl3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getUserGameLinks($user1);
        $this->assertCount(2, $result1);

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getUserGameLinks($user2);
        $this->assertCount(1, $result2);

        $result3 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getUserGameLinks($user3);
        $this->assertCount(0, $result3);
    }

    public function testGetGamesByUserIdMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail1@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'validpwd', 'validmail3@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $game2 = TestTools::getValidGame('testgame2');

        $gl1 = TestTools::getValidGameLink($user1, $game1);
        $gl2 = TestTools::getValidGameLink($user1, $game2);
        $gl3 = TestTools::getValidGameLink($user2, $game2);

        $arr = array($user1, $user2, $user3, $game1, $game2, $gl1, $gl2, $gl3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getGamesByUserId($user1);
        $this->assertCount(2, $result1);
        $this->assertEquals($result1[0]->getGame(), $game1);
        $this->assertEquals($result1[1]->getGame(), $game2);

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getGamesByUserId($user2);
        $this->assertCount(1, $result2);
        $this->assertEquals($result2[0]->getGame(), $game2);

        $result3 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getGamesByUserId($user3);
        $this->assertCount(0, $result3);
    }

    public function testGetResourcesForMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'validpwd', 'validmail1@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'validpwd', 'validmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'validpwd', 'validmail3@mail.com');
        $game1 = TestTools::getValidGame('testgame1');
        $game2 = TestTools::getValidGame('testgame2');

        $gl1 = TestTools::getValidGameLink($user1, $game1);
        $gl2 = TestTools::getValidGameLink($user1, $game2);
        $gl3 = TestTools::getValidGameLink($user2, $game2);
                
        
        $resources = $gl1->getResources();
        $resourceKeys = array('water', 'pureWater', 'steel', 'fuel');
        // set all resources to 10 for user1
        foreach ($resourceKeys as $key)
        {
            $resources[$key] = 10;
        }
        $gl1->setResources($resources);
        // to 20 for user 2
        foreach ($resourceKeys as $key)
        {
            $resources[$key] = 20;
        }
        $gl2->setResources($resources);
        
        // heroes required ~
        $heroSchema = TestTools::getValidHeroSchema('myHero');
        $hero1 = TestTools::getValidHero($heroSchema, $game1, $user1);
        $hero2 = TestTools::getValidHero($heroSchema, $game2, $user1);
        $hero3 = TestTools::getValidHero($heroSchema, $game2, $user2);

        $gl1->setHero($hero1);
        $gl2->setHero($hero2);
        $gl3->setHero($hero3);
        
        $arr = array($user1, $user2, $user3, $game1, $game2, $gl1, $gl2, $gl3,
                        $heroSchema, $hero1, $hero2, $hero3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $currentTime = time();
        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getResourcesFor($user1, $game1, $currentTime);
        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getResourcesFor($user1, $game2, $currentTime);
        $result3 = self::$doctrine->getRepository('EIPHRBundle:HRGameLink')->getResourcesFor($user2, $game2, $currentTime);
        $this->assertCount(12, $result1);
        $this->assertCount(12, $result2);
        $this->assertCount(12, $result3);
        foreach ($resourceKeys as $key)
        {
            $this->assertEquals(10, $result1[$key]);
            $this->assertEquals(20, $result2[$key]);
            $this->assertEquals(0, $result3[$key]);
        }
    }
}