<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;


class HRTechnologyRepositoryTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testGetTechnologyScore()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('testuser', 'testpassword', 'testmail@mail.com');
        $game = TestTools::getValidGame('testgame');
        $technoschema1 = TestTools::getValidTechnologySchema('testschema1');
        $technoschema1->setRValue(1);
        $technoschema2 = TestTools::getValidTechnologySchema('testschema2');
        $technoschema2->setRValue(2);
        $technoschema3 = TestTools::getValidTechnologySchema('testschema3');
        $technoschema3->setRValue(4);

        $technology1 = TestTools::getValidTechnology($technoschema1, $user, $game);

        $arr = array($user, $game, $technoschema1, $technoschema2, $technoschema3, $technology1);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $this->assertEquals(1, $result1);

        $technology2 = TestTools::getValidTechnology($technoschema2, $user, $game);
        $em->persist($technology2);
        $em->flush();
        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $this->assertEquals(3, $result2);

        $technology3 = TestTools::getValidTechnology($technoschema3, $user, $game);
        $em->persist($technology3);
        $em->flush();
        $result3 = self::$doctrine->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $this->assertEquals(7, $result3);

        $em->remove($technology1);
        $em->flush();
        $result4 = self::$doctrine->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $this->assertEquals(6, $result4);

    }
    
    public function testIsKnown()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $game = TestTools::getValidGame('game1');
        $user = TestTools::getValidUser('user', 'pass', 'user@gmail.com');
        $ts = TestTools::getValidTechnologySchema('test');
        foreach (array($game, $user, $ts) as $e) {
            $em->persist($e);
        }
        $em->flush();
        
        $this->assertFalse($em->getRepository('EIPHRBundle:HRTechnology')->isKnown($user, $game, $ts));
        $t = TestTools::getValidTechnology($ts, $user, $game);
        $em->persist($t);
        $em->flush();
        $this->assertTrue($em->getRepository('EIPHRBundle:HRTechnology')->isKnown($user, $game, $ts));
        $qt = new \EIP\HRBundle\Entity\HRTechnologyQueue($ts, $user, $game, time());
        $em->persist($qt);
        $em->flush();
        $this->assertTrue($em->getRepository('EIPHRBundle:HRTechnology')->isKnown($user, $game, $ts));
        $em->remove($t);
        $em->flush();
        $this->assertTrue($em->getRepository('EIPHRBundle:HRTechnology')->isKnown($user, $game, $ts));
        $em->remove($qt);
        $em->flush();
        $this->assertFalse($em->getRepository('EIPHRBundle:HRTechnology')->isKnown($user, $game, $ts));
        
    }

}