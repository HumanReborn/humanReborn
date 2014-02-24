<?php

use EIP\HRBundle\Utils\TestTools;
use EIP\HRBundle\Utils\ATestCase as ATestCase;

class HRQuestTest extends ATestCase
{

    public function testgetQuestFor()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        list($user1, $user2, $user3, $game, $game2) = $this->createQuestUpdateContext($em);

        $r1 = $em->getRepository('EIPHRBundle:HRQuest')->getQuestFor($user1, $game,
                EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT, EIP\HRBundle\Entity\HRQuest::STATE_ONGOING);
        $this->assertCount(1, $r1);
        $this->assertEquals($user1->getId(), $r1[0]->getUser()->getId());
        $this->assertEquals('destroyQuest', $r1[0]->getSchema()->getName());


        $r2 = $em->getRepository('EIPHRBundle:HRQuest')->getQuestFor($user1, $game,
                EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT, EIP\HRBundle\Entity\HRQuest::STATE_ONGOING);
        $this->assertCount(1, $r2);
        $this->assertEquals($user1->getId(), $r2[0]->getUser()->getId());
        $this->assertEquals('destroyQuest', $r2[0]->getSchema()->getName());


        $r3 = $em->getRepository('EIPHRBundle:HRQuest')->getQuestFor($user3, $game2,
                EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT, EIP\HRBundle\Entity\HRQuest::STATE_ONGOING);
        $this->assertCount(1, $r3);
        $this->assertEquals($user3->getId(), $r3[0]->getUser()->getId());
        $this->assertEquals('destroyQuest', $r3[0]->getSchema()->getName());
        // test with wrong game
         $r4 = $em->getRepository('EIPHRBundle:HRQuest')->getQuestFor($user3, $game,
                EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT, EIP\HRBundle\Entity\HRQuest::STATE_ONGOING);
        $this->assertEmpty($r4);
    }

    private function createQuestUpdateContext($em)
    {
        $currentTime = time();
        $game = TestTools::getValidGame('testGame');
        $game2 = TestTools::getValidGame('testGame2');
        $user1 = TestTools::getValidUser('testUser1', 'pass', 'testmail1@test.com');
        $user2 = TestTools::getValidUser('testUser2', 'pass', 'testmail2@test.com');
        $user3 = TestTools::getValidUser('testUser3', 'pass', 'testmail3@test.com');
        $gamelink1 = TestTools::getValidGameLink($user1, $game);
        $gamelink2 = TestTools::getValidGameLink($user2, $game);
        $gamelink3 = TestTools::getValidGameLink($user3, $game2);
        $town1 = TestTools::getValidTown('testTown1', $game, $user1, 10, 10);
        $town2 = TestTools::getValidTown('testTown2Attacked', $game, $user2, 12, 12);
        // hrtools::resolvefight needs the players to have heroes
        $us1 = TestTools::getValidUnitSchema('soldier');
        $us2 = TestTools::getValidUnitSchema('dog');

        $qs1 = new EIP\HRBundle\Entity\HRQuestSchema();
        $qs1->setDescription(''); $qs1->setOnce(false);
        $qs1->setName('buildQuest'); $qs1->setType(EIP\HRBundle\Entity\HRQuestSchema::BUILD);
        $qs1->setXpReward(0); $qs1->setData(array());

        $qs2 = new EIP\HRBundle\Entity\HRQuestSchema();
        $qs2->setDescription(''); $qs2->setOnce(false);
        $qs2->setName('destroyQuest'); $qs2->setType(EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT);
        $qs2->setXpReward(0); $qs2->setData(array($us1->getId()=>10, $us2->getId()=>3));

        foreach (array($game, $game2, $user1, $user2, $user3, $gamelink1, $gamelink2, $gamelink3,
                        $town1, $town2, $us1, $us2, $qs1, $qs2) as $e)
            $em->persist($e);
        // creating 12 quest for 3 users, 2 valid quests : one for user1, one for user2
        foreach (range(0,3) as $i)
        {
            foreach (array($user1, $user2, $user3) as $user)
            {
                $quest = new EIP\HRBundle\Entity\HRQuest();
                $quest->setSchema( $i < 2 ? $qs1 : $qs2);
                $quest->setUser($user);
                $quest->setGame($user == $user3 ? $game2 : $game);
                $quest->setGameLink($gamelink1);
                $quest->setState(
                        $i == 3 ? EIP\HRBundle\Entity\HRQuest::STATE_FINISHED :
                            EIP\HRBundle\Entity\HRQuest::STATE_ONGOING
                        );
                $em->persist($quest);
            }
        }
        $em->flush();

        // run the checkall command
        $cmd = new \EIP\HRBundle\Command\CheckAllCommand();
        $cmd->movementCompletion($em, $currentTime, self::$container->get('translator'));
        $em->flush();
        //
        return array(
          $user1, $user2, $user3, $game, $game2
        );
    }

    public function testIsCompleted() {
        $game = TestTools::getValidGame('testGame');
        $user1 = TestTools::getValidUser('testUser1', 'pass', 'testmail1@test.com');
        $gamelink1 = TestTools::getValidGameLink($user1, $game);
        $qs1 = new EIP\HRBundle\Entity\HRQuestSchema();
        $qs1->setDescription('');
        $qs1->setOnce(false);
        $qs1->setName('buildQuest');
        $qs1->setType(EIP\HRBundle\Entity\HRQuestSchema::BUILD);
        $qs1->setXpReward(0);
        $qs1->setData(array(42 => 42));

        $dt = new \Datetime('now');

        $quest = new EIP\HRBundle\Entity\HRQuest();
        $quest->setSchema($qs1);
        $quest->setUser($user1);
        $quest->setGame($game);
        $quest->setGameLink($gamelink1);

        $this->assertEquals($dt, $quest->getBeginTime());
        $this->assertEquals(EIP\HRBundle\Entity\HRQuest::STATE_ONGOING, $quest->getState());
        $this->assertEquals($qs1->getId(), $quest->getSchema()->getId());
        $this->assertFalse($quest->isCompleted());
        $this->assertEquals(EIP\HRBundle\Entity\HRQuest::STATE_ONGOING, $quest->getState());
        // complete the quest
        $quest->setData($qs1->getData());
        $this->assertTrue($quest->isCompleted());
        $this->assertEquals(EIP\HRBundle\Entity\HRQuest::STATE_VICTORY, $quest->getState());
    }

}

