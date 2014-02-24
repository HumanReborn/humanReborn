<?php

use EIP\HRBundle\Utils\TestTools;

class HRAchievementTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testNextAndPrev()
    {
        $em = self::$doctrine->getManager();
        EIP\HRBundle\Utils\TestTools::clearTables($em);
        $ac1 = new \EIP\HRBundle\Entity\HRAchievementSchema();
        $ac2 = new \EIP\HRBundle\Entity\HRAchievementSchema();
        $ac2->setName('achievement2');
        $ac2->setType(1);
        $ac2->setValue(1);
        $ac2->setPrev($ac1);
        $ac2->setNext(null);
        $ac1->setName('achievement1');
        $ac1->setType(1);
        $ac1->setValue(1);
        $ac1->setPrev(null);
        $ac1->setNext($ac2);
        $ac1->setStep(0);
        $ac2->setStep(1);
        $em->persist($ac1);
        $em->persist($ac2);
        $em->flush();
        $r = $em->createQuery("SELECT COUNT(ac.id) FROM EIPHRBundle:HRAchievementSchema ac")->getSingleScalarResult();
        $this->assertEquals(2, $r);
        $this->assertNull($ac2->getNext());
        $this->assertEquals($ac1, $ac2->getPrev());
        $this->assertNull($ac1->getPrev());
        $this->assertEquals($ac2, $ac1->getNext());
    }

    public function testCheckAchievementCompletion()
    {
        $em = self::$doctrine->getManager();
        EIP\HRBundle\Utils\TestTools::clearTables($em);
        $user = TestTools::getValidUser('username', 'password', 'email@test.com');
        $ac1 = new \EIP\HRBundle\Entity\HRAchievementSchema();
        $ac1->setName('achievement1');
        $ac1->setType(\EIP\HRBundle\Entity\HRAchievementSchema::NB_BUILDINGS);
        $acValue = 12;
        $ac1->setValue($acValue);
        $ac1->setPrev(null);
        $ac1->setNext(null);
        $ac1->setStep(0);

        foreach (array(0,1,5,10,12,20,1024) as $i) {
            $user->updateAchievementsProgression('nbBuildings', $i);
            if ($i < $acValue)
                $this->assertFalse($ac1->isAchieved($user));
            else
                $this->assertTrue($ac1->isAchieved($user));
        }
    }

}