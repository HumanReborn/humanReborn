<?php

use EIP\HRBundle\Utils\TestTools;
use EIP\HRBundle\Entity\HRUnit;
use EIP\HRBundle\Utils\HRTools;

class RecruitUnitTest extends \EIP\HRBundle\Utils\ATestCase
{
    private function getNbQueuedUnits($em, $garr) {
        return $em->createQuery('SELECT COUNT(q.id) FROM EIPHRBundle:HRUnitQueue q WHERE q.army = :armyid')
        ->setParameter(':armyid', $garr->getId())->getSingleScalarResult();
    }

    private function getNbQueuedUnitUsingSchema($em, $garr, $sc) {
        return $em->createQuery("SELECT COUNT(q.id) FROM EIPHRBundle:HRUnitQueue q WHERE q.army = :armyid AND q.schema = :schemaid")->setParameters(array('armyid' => $garr->getId(), ':schemaid' => $sc->getId()))->getSingleScalarResult();
    }

    private function getQueuedUnits($em, $garr) {
        return $em->createQuery('SELECT q FROM EIPHRBundle:HRUnitQueue q WHERE q.army = :armyid')->setParameter(':armyid', $garr->getId())->getResult();
    }

    public function testRecruit() {
        $em = self::$doctrine->getManager();
        $tr = self::$container->get('translator');
        TestTools::clearTables($em);

        $game = TestTools::getValidGame('game1');
        $user = TestTools::getValidUser('user1','1234','test@gmail.com');
        $gl   = TestTools::getValidGameLink($user, $game);
        $town = TestTools::getValidTown('town1', $game, $user);
        $sc   = TestTools::getValidUnitSchema('testSchema', 10);
        $garr = TestTools::getValidArmy($town);
        $garr->setGarrison(true);
        // schema cost
        $sc->setWaterCost(10);
        // add resources in the gamelink
        $resourceTypes = array('pureWater', 'water', 'steel', 'fuel');
        foreach ($resourceTypes as $type) {
            $gl->addResource($type, 1000);
        }
        foreach(array($user, $game, $gl, $town, $sc, $garr) as $e) {
            $em->persist($e);
        }
        $em->flush();


        $queuedUnitsCount = $this->getNbQueuedUnits($em, $garr);
        $this->assertEquals(0, $queuedUnitsCount);

        // recruit 5 units
        $res = HRTools::recruitUnit($sc->getId(), $town->getId(), $em, $tr, 5);
        // shoudl succeed
        $this->assertTrue($res);
        // properly added to the queue
        $em->refresh($gl);
        $queuedUnitsCount = $this->getNbQueuedUnits($em, $garr);
        $this->assertEquals(5, $queuedUnitsCount);
        $matchingQueueUnits = $this->getNbQueuedUnitUsingSchema($em, $garr, $sc);

        $this->assertEquals(5, $matchingQueueUnits);
        // resources
        $playerResources = $gl->getResources();
        $this->assertEquals(950, $playerResources['water']);
        $this->assertEquals(1000, $playerResources['pureWater']);
        $this->assertEquals(1000, $playerResources['steel']);
        $this->assertEquals(1000, $playerResources['fuel']);
        // completion time
        $qUnits = $this->getQueuedUnits($em, $garr);
        $this->assertEquals(5, count($qUnits));
        $lastTime = null;
        foreach ($qUnits as $q)
        {
            if ($lastTime === null)
                $lastTime = $q->getEndTime();
            else
            {
                $this->assertEquals($q->getEndTime(), ($lastTime + $sc->getBuildingTime()));
                $lastTime = $q->getEndTime();
            }
        }
        // checking queue limit
        $res = HRTools::recruitUnit($sc->getId(), $town->getId(), $em, $tr, 10);
        // should fail
        $this->assertFalse($res);
    }
}