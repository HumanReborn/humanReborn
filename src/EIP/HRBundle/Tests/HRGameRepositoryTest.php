<?php

use EIP\HRBundle\Utils\TestTools;

class HRGameRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{
    public function testGetGameListMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        foreach (range(0,9) as $i)
        {
            $game = TestTools::getValidGame('testgame'.$i);
            $em->persist($game);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRGame')->getGameList();
        $this->assertCount(10, $result1);
        foreach (range(0,9) as $i)
        {
            $this->assertEquals($result1[$i]->getName(), 'testgame'.$i);
        }

        // add a game and retest
        $newGame = TestTools::getValidGame('newGame');
        $em->persist($newGame);
        $em->flush();

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRGame')->getGameList();
        $this->assertCount(11, $result2);
        $this->assertEquals($result2[0], $newGame);
    }
}