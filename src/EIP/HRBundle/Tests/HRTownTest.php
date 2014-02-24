<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRTownTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testCreation()
    {
        $town = new \EIP\HRBundle\Entity\HRTown();
        $arr = array($town->getName(),
            $town->getXCoord(),
            $town->getYCoord(),
            $town->getOwner(),
            $town->getGame(),
            );
        foreach ($arr as $r)
        $this->assertNull($r);
    }

    public function testConstraints()
    {
        // get doctrine and empty the game and town tables table
        $em = self::$doctrine->getManager();

        // empty user, game and town repositories
        TestTools::clearTables($em);


        $town = new \EIP\HRBundle\Entity\HRTown();

        // 5 errors expected, fields can't be null
        $errors = self::$validator->validate($town);
        $this->assertCount(5, $errors);
        // set a Name and retest
        $town->setName('newTown');
        $errors = self::$validator->validate($town);
        $this->assertCount(4, $errors);
        // set coordinates and retest
        $town->setXCoord(1);
        $town->setYCoord(1);
        $errors = self::$validator->validate($town);
        $this->assertCount(2, $errors);
        // set a Game and retest
        $game = new \EIP\HRBundle\Entity\HRGame();
        $game->setName('testGame');
        $em = self::$doctrine->getManager();
        $em->persist($game);
        $em->flush();

        $town->setGame($game);
        $errors = self::$validator->validate($town);
        $this->assertCount(1, $errors);
        // set a User
        $user = new \EIP\HRBundle\Entity\HRUser();
        $user->setUsername('validUserName');
        $user->setPassword('aValidPassword');
        $user->setEmail('validEmail@validEmail.com');
        $em->persist($user);
        $em->flush();

        $town->setOwner($user);

        $errors = self::$validator->validate($town);
        $this->assertCount(0, $errors);
    }


    public function testPersist()
    {
        $em = self::$doctrine->getManager();
        // wipe the town table content and insert new user
        TestTools::clearTables($em);

        // create a HRUser and a HRGame for the test
        $user = new \EIP\HRBundle\Entity\HRUser();
        $user->setUsername('qwerty');
        $user->setPassword('qwerty42');
        $user->setEmail('validEmail42@validEmail.com');
        $game = new \EIP\HRBundle\Entity\HRGame();
        $game->setName('testGame');
        $em->persist($user);
        $em->persist($game);
        $em->flush();


        $town = new \EIP\HRBundle\Entity\HRTown();
        $town->setName('townName');
        $town->setGame($game);
        $town->setOwner($user);
        $town->setXCoord(1);
        $town->setYCoord(1);

        $em->persist($town);
        $em->flush();

        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRTown')->findOneBy(array('name' => 'townName'));

        // remove tmp user and game
        $em->remove($fetchedEntity); // removed too, can't delete the other two otherwise
        $em->remove($game);
        $em->remove($user);

        $em->flush();
    }

    public function testEdit()
    {
        $em = self::$doctrine->getManager();
        // wipe the town table content and insert new user
        TestTools::clearTables($em);

        // create a HRUser and a HRGame for the test
        $user = new \EIP\HRBundle\Entity\HRUser();
        $user->setUsername('qwerty');
        $user->setPassword('qwerty42');
        $user->setEmail('validEmail42@validEmail.com');
        $game = new \EIP\HRBundle\Entity\HRGame();
        $game->setName('testGame');
        $em->persist($user);
        $em->persist($game);
        $em->flush();

        // persist the town
        $town = new \EIP\HRBundle\Entity\HRTown();
        $town->setName('townName');
        $town->setGame($game);
        $town->setOwner($user);
        $town->setXCoord(1);
        $town->setYCoord(1);

        $em->persist($town);
        $em->flush();

        // retrieve and edit
        $toEdit = self::$doctrine->getRepository('EIPHRBundle:HRTown')->findOneBy(array('name' => 'townName'));
        $this->assertNotNull($toEdit);
        $toEdit->setName("EditedName");
        $em->flush();

        // retrieve one more time to check if the edit worked
        $checkEntity = self::$doctrine->getRepository('EIPHRBundle:HRTown')->findOneBy(array('name' => 'EditedName'));
        $this->assertNotNull($checkEntity);

        // remove tmp user and game
        $em->remove($checkEntity); // removed too, can't delete the other two otherwise
        $em->remove($game);
        $em->remove($user);

        $em->flush();
    }

}