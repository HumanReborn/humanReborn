<?php

use EIP\HRBundle\Utils\TestTools;

class HRArmyTest extends EIP\HRBundle\Utils\ATestCase
{
public function testCreation()
    {
        $user = TestTools::getValidUser('testUser', 'testPassword', 'testEmail@testEmail.com');
        $game = TestTools::getValidGame('testGame');
        $town = TestTools::getValidTown('testTown', $game, $user);
        $army = new \EIP\HRBundle\Entity\HRArmy($user, $game, $town);
        $this->assertFalse($army->getMoving());
        $this->assertFalse($army->isGarrison());
    }

    public function testPersistAndEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('testUser', 'testPassword', 'testEmail@testEmail.com');
        $game = TestTools::getValidGame('testGame');
        $town = TestTools::getValidTown('testTown', $game, $user);
        $army = new \EIP\HRBundle\Entity\HRArmy($user, $game, $town);

        foreach (array($user, $game, $town, $army) as $e)
                $em->persist($e);
        $em->flush();

        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->findOneBy(array(
            'user' => $user,
            'game' => $game,
            'town' => $town,
            'garrison' => false,
        ));
        $this->assertNotNull($fetchedEntity);
        //edit
        $fetchedEntity->setGarrison(true);
        $em->flush();
        $check = self::$doctrine->getRepository('EIPHRBundle:HRArmy')->findOneBy(array(
            'user' => $user,
            'game' => $game,
            'town' => $town,
            'garrison' => true,
        ));
        $this->assertNotNull($check);
    }
}