<?php

use EIP\HRBundle\Utils\TestTools;

class HRMessageRepositoryTest extends EIP\HRBundle\Utils\ATestCase
{
    public function testFetchAllMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'testpassword', 'testmail@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'testpassword', 'testmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'testpassword', 'testmail3@mail.com');

        $message1 = TestTools::getValidMessage($user1, $user2);
        $message2 = TestTools::getValidMessage($user2, $user1);
        $message3 = TestTools::getValidMessage($user1, $user2);

        $arr = array($user1, $user2, $user3, $message1, $message2, $message3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        $em->flush();

        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchAll();
        $this->assertCount(3, $result1);

        $message4 = TestTools::getValidMessage($user1, $user3);
        $em->persist($message4);
        $em->flush();

        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchAll();
        $this->assertCount(4, $result2);
    }

    public function testFetchInMessagesMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'testpassword', 'testmail@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'testpassword', 'testmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'testpassword', 'testmail3@mail.com');

        $arr = array($user1, $user2, $user3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        // insert 10 message from user2 to user1
        foreach (range(0, 9) as $i)
        {
            $m = TestTools::getValidMessage($user2, $user1);
            $em->persist($m);
        }

        $em->flush();
        // no fetch all optional parameter, 9 messages max
        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchInMessages($user1, false);
        $this->assertCount(9, $result1);
        // fetchall parameter 10 messages
        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchInMessages($user1, true);
        $this->assertCount(10, $result2);

        $result3 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchInMessages($user3, true);
        $this->assertCount(0, $result3);
    }

    public function testFetchOutMessagesMethod()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user1 = TestTools::getValidUser('testuser1', 'testpassword', 'testmail@mail.com');
        $user2 = TestTools::getValidUser('testuser2', 'testpassword', 'testmail2@mail.com');
        $user3 = TestTools::getValidUser('testuser3', 'testpassword', 'testmail3@mail.com');

        $arr = array($user1, $user2, $user3);
        foreach ($arr as $e)
        {
            $em->persist($e);
        }
        // insert 10 message from user2 to user1
        foreach (range(0, 9) as $i)
        {
            $m = TestTools::getValidMessage($user2, $user1);
            $em->persist($m);
        }

        $em->flush();
        // no fetch all optional parameter, 9 messages max
        $result1 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchOutMessages($user2, false);
        $this->assertCount(9, $result1);
        // fetchall parameter 10 messages
        $result2 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchOutMessages($user2, true);
        $this->assertCount(10, $result2);

        $result3 = self::$doctrine->getRepository('EIPHRBundle:HRMessage')->fetchOutMessages($user3, true);
        $this->assertCount(0, $result3);
    }


}