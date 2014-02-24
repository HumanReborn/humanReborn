<?php

use EIP\HRBundle\Utils\TestTools;
use EIP\HRBundle\Utils\ATestCase as ATestCase;

class HRBattleReportTest extends ATestCase
{
    private function createContext($em)
    {
        $game = TestTools::getValidGame('testGame');
        $user1 = TestTools::getValidUser('testUser1', 'pass', 'testmail1@test.com');
        $user2 = TestTools::getValidUser('testUser2', 'pass', 'testmail2@test.com');
        $gamelink1 = TestTools::getValidGameLink($user1, $game);
        $gamelink2 = TestTools::getValidGameLink($user2, $game);
        $town1 = TestTools::getValidTown('testTown1', $game, $user1, 10, 10);
        $town2 = TestTools::getValidTown('testTown2Attacked', $game, $user2, 12, 12);
        // hrtools::resolvefight needs the players to have heroes
        $heroschema = TestTools::getValidHeroSchema('testhero');
        $hero1 = TestTools::getValidHero($heroschema, $game, $user1);
        $hero2 = TestTools::getValidHero($heroschema, $game, $user2);
        $gamelink1->setHero($hero1);
        $gamelink2->setHero($hero2);

        $unitSchema = TestTools::getValidUnitSchema('testUnitSchema');
        $unitSchema->setAttack(0);
        $unitSchema->setArmor(0);
        $unitSchema->setHp(5);

        $attackerGarrison = TestTools::getValidArmy($town1);
        $attackerGarrison->setGarrison(true);
        $attackingArmy = TestTools::getValidArmy($town1);
        $defendingArmy = TestTools::getValidArmy($town2);
        $attackingArmy->setMoving(true);
        $defendingArmy->setGarrison(true);
        foreach (range(0,10) as $i)
        {
            $unit = new EIP\HRBundle\Entity\HRUnit();
            $unit->setSchema($unitSchema);
            if ($i < 3)
            {
                $unit->setArmy($attackingArmy);
                $attackingArmy->getUnits()->add($unit);
            }
            else
            {
                $unit->setArmy($defendingArmy);
                $defendingArmy->getUnits()->add($unit);
            }
            $em->persist($unit);
        }
        foreach (array($game, $user1, $user2, $gamelink1, $gamelink2, $town1, $town2, $unitSchema,
                        $attackerGarrison, $attackingArmy, $defendingArmy, $heroschema,
                        $hero1, $hero2) as $e){
            $em->persist($e);
        }
        // attacking movement
        $currentTime = time();
        $m = $attackingArmy->getMovement();
        $m->setEndTime($currentTime);
        $m->setFromX(10);
        $m->setFromy(10);
        $m->setToX(12);
        $m->setToY(12);
        $m->setFinished(false);

        $em->flush();
        // run the checkall command
        $cmd = new \EIP\HRBundle\Command\CheckAllCommand();
        $cmd->movementCompletion($em, $currentTime, self::$container->get('translator'));
        $em->flush();
        //
        return array(
          $user1, $user2,
            $game,
            $town2,
            $unitSchema
        );
    }

    public function testCreation()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $BattleReports = $em->getRepository('EIPHRBundle:HRBattleReport')->findAll();
        $this->assertCount(0, $BattleReports);
        list($user1, $user2, $game, $town2, $unitSchema) = $this->createContext($em); // creating context
        $BattleReports = $em->getRepository('EIPHRBundle:HRBattleReport')->findAll();
        $this->assertCount(1, $BattleReports);

        $this->assertEquals($BattleReports[0]->getAttacker(), $user1);
        $this->assertEquals($BattleReports[0]->getDefender(), $user2);
        $this->assertEquals($game, $game);
        $this->assertEquals($BattleReports[0]->getTown(), $town2);
        $this->assertEquals($BattleReports[0]->getXpWon(), 15); // schema->getHp * nb unit destroyed
        $this->assertEquals($BattleReports[0]->getWinner(), $user2);
        // check if the losing army has been deleted ( if not a garrison )
        $result = $em->createQuery("SELECT COUNT(a.id) FROM EIPHRBundle:HRArmy a WHERE a.game = :gameid and a.garrison = false")
                ->setParameter(':gameid', $game->getId())
                ->getSingleScalarResult();
        $this->assertEquals(0, $result);
        // check if the battle report 'armies' field has been properly filled
        $armies = $BattleReports[0]->getArmies();

        $em->refresh($unitSchema);

        $A = $armies[$user1->getId()][$unitSchema->getId()];
        $B = $armies[$user2->getId()][$unitSchema->getId()];

        $this->assertEquals(3, $A['number']);
        $this->assertEquals(8, $B['number']);
    }
}
