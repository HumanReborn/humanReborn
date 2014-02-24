<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRTechnologyTest extends \EIP\HRBundle\Utils\ATestCase
{
    /**
     * \brief test persistence and edition
     */
    public function testPersistAndEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('bob', 'bob42', 'bobmail@mail.com');
        $game = TestTools::getValidGame('testGame');
        $schema = TestTools::getValidTechnologySchema('testSchema');

        $entities = array($game, $user, $schema);
        foreach ($entities as $e)
        {
            $em->persist($e);
        }

        $t = new \EIP\HRBundle\Entity\HRTechnology();
        $t->setSchema($schema);
        $t->setGame($game);

        $em->persist($t);
        $em->flush();

        //
        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRTechnology')->findOneBy(array(
            'schema' => $schema,
            'game' => $game,
            ));
        $this->assertNotNull($fetchedEntity);

        // EDIT
        $tmpSchema = TestTools::getValidTechnologySchema('tmpSchema');
        $em->persist($tmpSchema);

        $t->setSchema($tmpSchema);
        $em->flush();

        $checkEntity =  self::$doctrine->getRepository('EIPHRBundle:HRTechnology')->findOneBy(array(
            'schema' => $tmpSchema,
            'game' => $game,
            ));
        $this->assertNotNull($checkEntity);
    }

    /**
     * \brief test if the buff associated with a technology is correctly persisted in the database once the technology has been researched
     */
    public function testTechnologyBuff() {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $user = TestTools::getValidUser('bob', 'bob42', 'bobmail@mail.com');
        $game = TestTools::getValidGame('testGame');
        $gl = TestTools::getValidGameLink($user, $game);
        $schema = TestTools::getValidTechnologySchema('testSchema');
        $buffSchema = new \EIP\HRBundle\Entity\HRBuffSchema();
        $buffSchema->setName('testbuffschema');
        $buffSchema->setType(\EIP\HRBundle\Entity\HRBuffSchema::ARMOR_ALL_TYPE);
        $buffSchema->setPermanent(true);
        $buffSchema->setDuration(0);
        $buffSchema->setValue(2);
        $schema->setBuffSchema($buffSchema);

        $q = new \EIP\HRBundle\Entity\HRTechnologyQueue($schema, $user, $game, time());
        foreach (array($user, $game, $gl, $schema, $buffSchema, $q) as $e)
            $em->persist($e);
        $em->flush();

        $technos = $em->getRepository('EIPHRBundle:HRTechnology')->findAll();
        $buffs = $em->getRepository('EIPHRBundle:HRBuff')->findAll();
        $this->assertCount(0, $technos);
        $this->assertCount(0, $buffs);

        $cmd = new \EIP\HRBundle\Command\CheckAllCommand();
        $cmd->technologyCompletion($em, time());
        $em->flush();

        $technos = $em->getRepository('EIPHRBundle:HRTechnology')->findAll();
        $buffs = $em->getRepository('EIPHRBundle:HRBuff')->findAll();
        $this->assertCount(1, $technos);
        $this->assertCount(1, $buffs);
        $this->assertEquals($schema, $technos[0]->getSchema());
        $this->assertEquals($buffSchema, $buffs[0]->getSchema());
    }

}