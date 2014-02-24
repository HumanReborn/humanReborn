<?php

use EIP\HRBundle\Utils\TestTools;

class HRHeroTest extends EIP\HRBundle\Utils\ATestCase
{
    /**
    *        \fn  void testCreation()
    *        \brief Test if HRHero class is correctly created
    */
    public function testCreation()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $user = TestTools::getValidUser('user1', 'pwd', 'email1@truc.com');
        $game = TestTools::getValidGame('game1');
        $gamelink = TestTools::getValidGameLink($user, $game);
        $heroSchema = TestTools::getValidHeroSchema('chuck');
        $hero = TestTools::getValidHero($heroSchema, $game, $user);
        $arr = array($user, $game, $gamelink, $heroSchema, $hero);
        foreach ($arr as $e)
            $em->persist($e);
        $em->flush();
        $h = $em->getRepository('EIPHRBundle:HRHero')->findOneBy(array(
            'user' => $user,
            'game' => $game,
            'schema' => $heroSchema
        ));

        $this->assertNotNull($h->getItems());
        $this->assertEquals($h->getSchema(), $heroSchema);
        $this->assertEquals($h->getSchema()->getDescription(), $h->getSchema()->getName().'.desc');
        $this->assertEquals($h->getGame(), $game);
        $this->assertEquals($h->getLevel(), 1);
        $this->assertEquals($h->getUser(), $user);
    }

    /**
     * \brief test if the hero gains xp points and level up
     */
    public function testGainXp()
    {
         $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $user = TestTools::getValidUser('user1', 'pwd', 'email1@truc.com');
        $game = TestTools::getValidGame('game1');
        $gamelink = TestTools::getValidGameLink($user, $game);
        $heroSchema = TestTools::getValidHeroSchema('chuck');
        $hero = TestTools::getValidHero($heroSchema, $game, $user);
        $arr = array($user, $game, $gamelink, $heroSchema, $hero);
        foreach ($arr as $e)
            $em->persist($e);
        $em->flush();
        $h = $em->getRepository('EIPHRBundle:HRHero')->findOneBy(array(
            'user' => $user,
            'game' => $game,
            'schema' => $heroSchema
        ));

        $this->assertEquals(1, $h->getCurrentXp());
        $this->assertEquals(1, $h->getLevel());
        $b1 = $h->receiveXp(80);
        $this->assertFalse($b1); // has not leveled up
        $this->assertEquals(81, $h->getCurrentXp());
        $this->assertEquals(1, $h->getLevel());
        $b2 = $h->receiveXp(1000);
        $this->assertTrue($b2);
        $this->assertEquals(2, $h->getLevel());
        $this->assertEquals(1081, $h->getCurrentXp());
    }
}