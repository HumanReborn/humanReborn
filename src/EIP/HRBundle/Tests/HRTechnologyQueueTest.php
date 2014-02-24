<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRTechnologyQueueTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testPersistAndEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $user = TestTools::getValidUser('bob', 'bob42', 'bobmail@mail.com');
        $game = TestTools::getValidGame('testGame');
        $town = TestTools::getValidTown('testTown', $game, $user);
        $schema = TestTools::getValidTechnologySchema('testSchema');

        $entities = array($town, $game, $user, $schema);
        foreach ($entities as $e)
        {
            $em->persist($e);
        }

        $startTime = time();
        $q = new \EIP\HRBundle\Entity\HRTechnologyQueue($schema, $user, $game, $startTime);
        $em->persist($q);
        $em->flush();

        //
        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRTechnologyQueue')->findOneBy(array(
            'game' => $game,
            'user' => $user
            ));
        $this->assertNotNull($fetchedEntity);

        // EDIT
        $user2 = TestTools::getValidUser('user2', 'user2pwd', 'user2@testmail.com');
        $em->persist($user2);
        $fetchedEntity->setUser($user2);
        $em->flush();


        $checkEntity =  self::$doctrine->getRepository('EIPHRBundle:HRTechnologyQueue')->findOneBy(array(
            'game' => $game,
            'user' => $user2
            ));
        $this->assertNotNull($checkEntity);
    }



}