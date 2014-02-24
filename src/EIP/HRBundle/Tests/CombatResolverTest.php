<?php

use EIP\HRBundle\Utils\TestTools;
use EIP\HRBundle\Entity\HRUnit;

class CombatResolverTest extends \EIP\HRBundle\Utils\ATestCase
{

    private function createContext(Doctrine\ORM\EntityManager $em)
    {
        $game = TestTools::getValidGame('testGame');
        $user1 = TestTools::getValidUser('testUser1', 'pass', 'testmail1@test.com');
        $user2 = TestTools::getValidUser('testUser2', 'pass', 'testmail2@test.com');
        $gamelink1 = TestTools::getValidGameLink($user1, $game);
        $gamelink2 = TestTools::getValidGameLink($user2, $game);
        $town1 = TestTools::getValidTown('testTown1', $game, $user1, 10, 10);
        $town2 = TestTools::getValidTown('testTown2Attacked', $game, $user2, 12, 12);
        //

        $heroSchema1 = TestTools::getValidHeroSchema('testhero1');
        $heroSchema1->setBonusAttack(0.3);
        $heroSchema2 = TestTools::getValidHeroSchema('testhero2');
        $heroSchema2->setBonusArmor(0.2);
        $hero1 = TestTools::getValidHero($heroSchema1, $game, $user1);
        $hero2 = TestTools::getValidHero($heroSchema2, $game, $user2);

        $gamelink1->setHero($hero1);
        $gamelink2->setHero($hero2);
        // buffs
        $buffSchema1 = new \EIP\HRBundle\Entity\HRBuffSchema(true); // permanent
        $buffSchema1->setName('attackBuff0.5');
        $buffSchema1->setValue(0.5);
        $buffSchema1->setDuration(0);
        $buffSchema1->setType(\EIP\HRBundle\Entity\HRBuffSchema::ATTACK_ALL_TYPE);
        $buffSchema2 = new \EIP\HRBundle\Entity\HRBuffSchema(true); // permanent
        $buffSchema2->setName('armorBuff0.2');
        $buffSchema2->setValue(0.2);
        $buffSchema2->setDuration(0);
        $buffSchema2->setType(\EIP\HRBundle\Entity\HRBuffSchema::ARMOR_ALL_TYPE);

        $buff1 = new EIP\HRBundle\Entity\HRBuff($buffSchema1, $gamelink1);
        $buff2 = new EIP\HRBundle\Entity\HRBuff($buffSchema2, $gamelink1);
        $buff3 = new EIP\HRBundle\Entity\HRBuff($buffSchema2, $gamelink2);

        // units & army
        $army1 = TestTools::getValidArmy($town1);
        $army2 = TestTools::getValidArmy($town2);
        $unitschema = TestTools::getValidUnitSchema('testunitschema');
        $unitschema->setArmor(1);
        $unitschema->setAttack(2);
        $unitschema->setHp(4);
        foreach (range(0,10) as $i) {
            $unit = new \EIP\HRBundle\Entity\HRUnit();
            $unit->setSchema($unitschema);
            if ($i < 4)
            {
                $unit->setArmy($army1);
                $army1->getUnits()->add($unit);
            }
            else
            {
                $unit->setArmy($army2);
                $army2->getUnits()->add($unit);
            }
            $em->persist($unit);
        }

        foreach (array($game, $user1, $user2, $gamelink1, $gamelink2, $town1, $town2,
            $buffSchema1, $buffSchema2, $buff1, $buff2, $buff3, $heroSchema1,
            $heroSchema2, $hero1, $hero2, $unitschema, $army1, $army2) as $e){
            $em->persist($e);
        }
        $em->flush();
        return array(
          $user1, $user2,
            $game,
            $army1,
            $army2
        );
    }

    private function internalTestFightingScore($army1, $army2, $user1buffs, $user2buffs, $em)
    {
        $afs = $army1->getFightingScore($user1buffs, $em);
        $dfs = $army2->getFightingScore($user2buffs, $em);
        $this->assertEquals(4 * (2 + 0.8), $afs); // 4 units and attack buff
        $this->assertEquals(7 * (2 + 0.0), $dfs); // 7 units, no attack buff
    }

    public function testHeroBuffRetrieval()
    {
        $em = self::$doctrine->getManager();
        $currentTime = time();
        TestTools::clearTables($em);
        list($user1, $user2, $game, $army1, $army2) = $this->createContext($em);
        $user1buffs = $em->getRepository('EIPHRBundle:HRGameLink')->getUserTotalBuffs($user1, $game, $currentTime);
        $user2buffs = $em->getRepository('EIPHRBundle:HRGameLink')->getUserTotalBuffs($user2, $game, $currentTime);
        $this->assertEquals(0.8, $user1buffs['attack']);
        $this->assertEquals(0.2, $user1buffs['armor']);
        $this->assertEquals(0.0, $user1buffs['hp']);
        $this->assertEquals(0.0, $user2buffs['attack']);
        $this->assertEquals(0.4, $user2buffs['armor']);
        $this->assertEquals(0.0, $user2buffs['hp']);
        $this->internalTestFightingScore($army1, $army2, $user1buffs, $user2buffs, $em);
    }

    public function testCombatPerformance() {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        // context
        $game = TestTools::getValidGame('testGame');
        $user1 = TestTools::getValidUser('testUser1', 'pass', 'testmail1@test.com');
        $user2 = TestTools::getValidUser('testUser2', 'pass', 'testmail2@test.com');
        $gamelink1 = TestTools::getValidGameLink($user1, $game);
        $gamelink2 = TestTools::getValidGameLink($user2, $game);
        $town1 = TestTools::getValidTown('testTown1', $game, $user1, 10, 10);
        $town2 = TestTools::getValidTown('testTown2Attacked', $game, $user2, 12, 12);

        $heroSchema1 = TestTools::getValidHeroSchema('testhero1');
        $heroSchema1->setBonusAttack(0.3);
        $heroSchema2 = TestTools::getValidHeroSchema('testhero2');
        $heroSchema2->setBonusArmor(0.2);
        $hero1 = TestTools::getValidHero($heroSchema1, $game, $user1);
        $hero2 = TestTools::getValidHero($heroSchema2, $game, $user2);

        $gamelink1->setHero($hero1);
        $gamelink2->setHero($hero2);
        $army1 = TestTools::getValidArmy($town1);
        $army2 = TestTools::getValidArmy($town2);
        $unitschema = TestTools::getValidUnitSchema('testunitschema');
        $unitschema->setArmor(1);
        $unitschema->setAttack(2);
        $unitschema->setHp(4);

        foreach (array($game, $user1, $user2, $gamelink1, $gamelink2, $town1, $town2,
            $heroSchema1,$heroSchema2, $hero1, $hero2, $unitschema, $army1, $army2) as $e){
            $em->persist($e);
        }
        $em->flush();
        $mtimeBegin = microtime(true);
        foreach (range(0,1000) as $i) {
            $u1 = new \EIP\HRBundle\Entity\HRUnit();
            $u1->setSchema($unitschema);
            $army1->getUnits()->add($u1);
            $em->persist($u1);

            if ($i > 0) {
                $u2 = new \EIP\HRBundle\Entity\HRUnit();
                $u2->setSchema($unitschema);
                $army2->getUnits()->add($u2);
                $em->persist($u2);
            }
        }
        $mtimeEnd = microtime(true);
        // echo("\nPersist time:\n");
        // echo($mtimeEnd - $mtimeBegin);
        // echo("\n\n");
        $mtimeBegin = $mtimeEnd;

        $em->flush();
        $mtimeEnd = microtime(true);
        // echo "\nflush time:\n\n";
        // echo($mtimeEnd - $mtimeBegin);
        // echo("\n\n");
        //
        $tr = self::$container->get('translator');
        $currentTime=time();
        $mtimeBegin = microtime(true);
        \EIP\HRBundle\Utils\HRTools::resolveFight($army1, $army2, $em, $currentTime, $tr);
        $mtimeEnd = microtime(true);
        // echo "\nRESOLVE FIGHT time:\n\n";
        // echo($mtimeEnd - $mtimeBegin);
        // echo("\n\n");
        $mtimeBegin = $mtimeEnd;
        $em->flush();
        $mtimeEnd = microtime(true);
        $ftime = $mtimeEnd - $mtimeBegin;
        // echo("\nFINALFLUSHTIME: $ftime\n\n");
    }

    public function testTownCapture() {
        $tr = self::$container->get('translator');
        $em = self::$doctrine->getManager();
        $currentTime = time();
        TestTools::clearTables($em);
        list($user1, $user2, $game, $army1, $army2) = $this->createContext($em);
        $army2->setGarrison(true);

        // add town for player 2 ( reassign town test )
        $shouldBeReassignedArmy1 = TestTools::getValidArmy($army2->getTown());
        $shouldBeReassignedArmy2 = TestTools::getValidArmy($army2->getTown());
        $em->persist($shouldBeReassignedArmy1);
        $em->persist($shouldBeReassignedArmy2);
        $t1 = TestTools::getValidTown('nearest', $game, $user2, 42, 42);
        $t2 = TestTools::getValidTown('medium', $game, $user2, 100, 100);
        $t3 = TestTools::getValidTown('furthest', $game, $user2, 300, 300);
        foreach(array($t1, $t2, $t3) as $t)
            $em->persist($t);
        $em->flush();


        $townToBeCaptured = $army2->getTown();
        $this->assertEquals($townToBeCaptured->getOwner()->getId(), $army2->getUser()->getId());

        // adding buildingqueue to the town
        $buildingSchema = TestTools::getValidBuildingSchema("stuff");
        $bq = TestTools::getValidBuildingQueue($townToBeCaptured, $buildingSchema, 42);
        $em->persist($buildingSchema);
        $em->persist($bq);
        $em->flush();
        $em->refresh($townToBeCaptured); // force entity refresh ~
        $this->assertEquals(1, $townToBeCaptured->getBuildingsQueue()->count());

        // make sure army1 wins ~
        $army1Units = $army1->getUnits();
        $unitCount = $army1Units->count();
        $schema = $army1Units[0]->getSchema();
        foreach (range(1, 50) as $i) {
            $newUnit = new HRUnit();
            $newUnit->setSchema($schema);
            $newUnit->setArmy($army1);
            $army1->getUnits()->add($newUnit);
            $em->persist($newUnit);
        }
        $em->flush();
        $this->assertFalse($shouldBeReassignedArmy1->getTown()->getId() != $townToBeCaptured->getId());
        $this->assertFalse($shouldBeReassignedArmy2->getTown()->getId() != $townToBeCaptured->getId());
        $this->assertTrue($army1->getUnits()->count() > $army2->getUnits()->count());
        \EIP\HRBundle\Utils\HRTools::resolveFight($army1, $army2, $em, $currentTime, $tr);
        $em->flush();

        // checking if army1 has won
        $this->assertTrue($army1->getUnits()->count() > 0);
        $this->assertEquals(0, $army2->getUnits()->count());
        // checking if the buildingqueue entities have properly been cleaned
        $em->refresh($townToBeCaptured); // force entity refresh ~
        $this->assertEquals($townToBeCaptured->getOwner()->getId(), $army1->getUser()->getId());
        $this->assertEquals(0, $townToBeCaptured->getBuildingsQueue()->count());
        // check if the winnign army is assigned to the conquered town
        $this->assertEquals($townToBeCaptured->getId(), $army1->getTown()->getId());
        // checking if the armies link to this town have been reassigned
        $armies = $em->getRepository('EIPHRBundle:HRArmy')->findBy(array('user' => $user2));
        foreach ($armies as $a) {
            $em->refresh($a);
            $this->assertTrue($a->getTown()->getId() != $townToBeCaptured->getId());
        }
        $this->assertTrue($shouldBeReassignedArmy1->getTown()->getId() != $townToBeCaptured->getId());
        $this->assertEquals($shouldBeReassignedArmy1->getTown()->getId(), $t1->getId());
        $this->assertTrue($shouldBeReassignedArmy2->getTown()->getId() != $townToBeCaptured->getId());
        $this->assertEquals($shouldBeReassignedArmy2->getTown()->getId(), $t1->getId());
        // check if the new empty garrison is properly created
        $r = $em->createQuery("SELECT COUNT(a.id)
                                FROM EIPHRBundle:HRArmy a
                                WHERE a.town = :townid
                                AND a.garrison = true
                                AND a.user = :user1id")
                ->setParameter(':townid',$townToBeCaptured->getId())
                ->setParameter(':user1id',$user1->getId())
                ->getSingleScalarResult();
        $this->assertEquals(1, $r);
    }

    public function testCheckGameOver() 
    {
       $tr = self::$container->get('translator');
       $em = self::$doctrine->getManager();
       $currentTime = time();
       TestTools::clearTables($em);
       // CASE 1 -- No clan - Check if user1 has won (before capture | after capture)
       list($user1, $user2, $game, $army1, $army2) = $this->createContext($em);
       $result = $em->getRepository('EIPHRBundle:HRTown')->checkUserVictory($user1, $game);
       $this->assertFalse($result);
       $army2->getTown()->setOwner($user1);
       $em->flush();
       $result = $em->getRepository('EIPHRBundle:HRTown')->checkUserVictory($user1, $game);
       $this->assertTrue($result);
       // CASE 2 -- clans
       $army2->getTown()->setOwner($user2);
       $em->flush();

       $clan1 = new \EIP\HRBundle\Entity\HRClan();
       $clan1->setName('clan1'); 
       $clan1->setAcronym("A"); 
       $clan1->setRecruitmentStatut(false);
       $clan1->setIdGame($game->getId());
       $em->persist($clan1);
       $em->flush();
       $cm = new \EIP\HRBundle\Entity\HRClanMembers($clan1->getId(), $user1->getId(), 42);
       $em->persist($cm);
       $em->flush();
       // user1 belongs to <clan1>, user2 has no clan
       $result = $em->getRepository('EIPHRBundle:HRTown')->checkClanVictory($clan1, $game);
       $this->assertFalse($result);
       // adding user2 to <clan1>
       $cm = new \EIP\HRBundle\Entity\HRClanMembers($clan1->getId(), $user2->getId(), 42);
       $em->persist($cm);
       $em->flush();   
       $result = $em->getRepository('EIPHRBundle:HRTown')->checkClanVictory($clan1, $game);
       $this->assertTrue($result);
    }

}
