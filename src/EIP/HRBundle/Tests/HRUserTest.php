<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;


class HRUserTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testCreation()
    {
        $user = new \EIP\HRBundle\Entity\HRUser();

        $this->assertNotNull($user->getSalt());
        $this->assertTrue(strlen($user->getSalt()) > 0);
        $this->assertNotNull($user->getLastLogin());
        $this->assertEquals($user->getLastLogin(), $user->getCreatedOn());
        $this->assertEquals($user->getStatus(), \EIP\HRBundle\Entity\HRUser::STATUS_PENDING);
        $arr = array('ROLE_USER');
        $this->assertEquals($user->getRoles(), $arr);
    }

    public function testPersist()
    {
        $user = new \EIP\HRBundle\Entity\HRUser();

        $errors = self::$validator->validate($user);
        $this->assertTrue(count($errors) == 3);

        // enter a valid username and validate
        $user->setUsername('tmp');
        $errors = self::$validator->validate($user);
        $this->assertTrue(count($errors) == 2);

        // enter a valid password and validate
        $user->setPassword('42psdas');
        $errors = self::$validator->validate($user);
        $this->assertTrue(count($errors) == 1);

        // enter a valid email and validate
        $user->setEmail('toto@gmail.com');
        $errors = self::$validator->validate($user);
        $this->assertTrue(count($errors) == 0);

        /*
         *  persist the valid entity
         */
        // get doctrine and empty the user table
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $result = $em->createQuery("SELECT COUNT(u)  FROM EIPHRBundle:HRUser u")->getSingleScalarResult();
        $this->assertEquals($result, 0);
        // insert
        $em->persist($user);
        $em->flush();
        // check
        $result = $em->createQuery("SELECT COUNT(u)  FROM EIPHRBundle:HRUser u")->getSingleScalarResult();
        $this->assertEquals($result, 1);
        // remove it and recheck
        $em->remove($user);
        $em->flush();
        $result = $em->createQuery("SELECT COUNT(u)  FROM EIPHRBundle:HRUser u")->getSingleScalarResult();
        $this->assertEquals($result, 0);
    }


    public function testUnique()
    {
        $em = self::$doctrine->getManager();
        // wipe the user db content
        TestTools::clearTables($em);
        // insert user 1, no problem
        $user1 = new \EIP\HRBundle\Entity\HRUser();
        $user1->setUsername('bobby');
        $user1->setPassword('aaaaaaddddddd');
        $user1->setEmail('example@example.com');
        $em->persist($user1);
        $em->flush();
        // insert user 2 -- duplicate username
        $user2 = new \EIP\HRBundle\Entity\HRUser();
        $user2->setUsername('bobby');
        $user2->setPassword('zzzzzzzzxxxxxxxx');
        $user2->setEmail('zozo@popo.com');
        $errors = self::$validator->validate($user2);
        $this->assertCount(1, $errors);
        // change username to new one - no errors
        $user2->setUsername('newUsername');
        $errors = self::$validator->validate($user2);
        $this->assertCount(0, $errors);
        // set email equal to user1 -- error
        $user2->setEmail($user1->getEmail());
        $errors = self::$validator->validate($user2);
        $this->assertCount(1, $errors);
    }

    public function testEdit()
    {
        $em = self::$doctrine->getManager();
        // wipe the user db content and insert new user
        TestTools::clearTables($em);
        $user = new \EIP\HRBundle\Entity\HRUser();
        $user->setUsername('user1');
        $user->setPassword('aaaaaaddddddd');
        $user->setEmail('user1@user1.com');
        $em->persist($user);
        $em->flush();
        // retrieve the user from the database
        $toEdit = $em->getRepository('EIPHRBundle:HRUser')->findOneByUsername('user1');
        $this->assertNotNull($toEdit);
        $this->assertNotNull($toEdit);
        $this->assertEquals($toEdit->getUsername(), 'user1');
        $toEdit->setUsername('tmpName');
        $em->flush();
        // check if the edit worked -- exception thrown if not found
        $editedUser = $em->getRepository('EIPHRBundle:HRUser')->findOneByUsername('tmpName');
        $this->assertNotNull($editedUser);
    }

    public function testConstraints()
    {
        $em = self::$doctrine->getManager();
        // wipe the user db content
        TestTools::clearTables($em);
        $user = new \EIP\HRBundle\Entity\HRUser();
        $user->setUsername('user1');
        $user->setPassword('aaaaaaddddddd');
        $user->setEmail('user1@user1.com');
        // user is valid
        $errors = self::$validator->validate($user);
        $this->assertCount(0, $errors);

        // username too short
        $user->setUsername('u');
        $errors = self::$validator->validate($user);
        $this->assertCount(1, $errors);
        // username too long
        $user->setUsername('user1user1user1user1user1user1user1user1user1user1user1user1user1user1user1user1user1');
        $errors = self::$validator->validate($user);
        $this->assertCount(1, $errors);
        $user->setUsername('user1');

        // password too short
        $user->setPassword('n');
        $errors = self::$validator->validate($user);
        $this->assertCount(1, $errors);
        // password too long
        $len = 129;
        $user->setPassword(substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', $len)), 0, $len));
        $errors = self::$validator->validate($user);
        $this->assertCount(1, $errors);
        $user->setPassword('oldpassword');

        // email
        $user->setEmail('NotAValidEmail.lol');
        $errors = self::$validator->validate($user);
        $this->assertCount(1, $errors);
    }

    public function testHasAchieved()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $user = TestTools::getValidUser('username', 'password', 'mailol@mail.com');
        $acSchema1 = new \EIP\HRBundle\Entity\HRAchievementSchema();
        $acSchema2 = new \EIP\HRBundle\Entity\HRAchievementSchema();
        $acSchema1->setName('schema1');
        $acSchema1->setNext(null);
        $acSchema1->setPrev(null);
        $acSchema1->setType(\EIP\HRBundle\Entity\HRAchievementSchema::HERO_MAX_LEVEL);
        $acSchema1->setValue(3);
        $acSchema1->setStep(0);

        $acSchema2->setName('schema2');
        $acSchema2->setNext(null);
        $acSchema2->setPrev(null);
        $acSchema2->setType(\EIP\HRBundle\Entity\HRAchievementSchema::NB_BUILDINGS);
        $acSchema2->setValue(7);
        $acSchema2->setStep(0);

        foreach (array($user, $acSchema1, $acSchema2) as $e)
            $em->persist($e);
        $em->flush();
        $this->assertEquals(0, $user->getAchievements()->count());
        $this->assertFalse($user->hasAchieved($acSchema1->getId()));
        $this->assertFalse($user->hasAchieved($acSchema2->getId()));
        $ac = new \EIP\HRBundle\Entity\HRAchievement();
        $ac->setUser($user);
        $ac->setSchema($acSchema2);
        $user->getAchievements()->add($ac);
        $em->persist($ac);
        $em->flush();
        $this->assertEquals(1, $user->getAchievements()->count());
        $this->assertFalse($user->hasAchieved($acSchema1->getId()));
        $this->assertTrue($user->hasAchieved($acSchema2->getId()));
        $ac = new \EIP\HRBundle\Entity\HRAchievement();
        $ac->setUser($user);
        $ac->setSchema($acSchema1);
        $user->getAchievements()->add($ac);
        $em->persist($ac);
        $em->flush();
        $this->assertEquals(2, $user->getAchievements()->count());
        $this->assertTrue($user->hasAchieved($acSchema1->getId()));
        $this->assertTrue($user->hasAchieved($acSchema2->getId()));
    }


}