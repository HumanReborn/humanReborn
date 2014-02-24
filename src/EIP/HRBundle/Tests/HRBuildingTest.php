<?php

namespace EIP\HRBundle\Tests;

use EIP\HRBundle\Utils\TestTools;

class HRBuildingTest extends \EIP\HRBundle\Utils\ATestCase
{

    public function testPersistAndEdit()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);

        $user = TestTools::getValidUser('bob', 'bob42', 'bobmail@mail.com');
        $game = TestTools::getValidGame('testGame');
        $town = TestTools::getValidTown('testTown', $game, $user);
        $schema = TestTools::getValidBuildingSchema('testSchema');

        $entities = array($town, $game, $user, $schema);
        foreach ($entities as $e)
        {
            $em->persist($e);
        }

        $b = new \EIP\HRBundle\Entity\HRBuilding();
        $b->setSchema($schema);
        $b->setTown($town);

        $em->persist($b);
        $em->flush();

        // exception thrown if not found -- see findOneBy() ...
        $fetchedEntity = self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->findOneBy(array(
            'schema' => $schema,
            'town' => $town,
            ));
        $this->assertNotNull($fetchedEntity);

        // EDIT
        $tmpSchema = TestTools::getValidBuildingSchema('tmpSchema');
        $em->persist($tmpSchema);

        $b->setSchema($tmpSchema);
        $em->flush();


        $checkEntity =  self::$doctrine->getRepository('EIPHRBundle:HRBuilding')->findOneBy(array(
            'schema' => $tmpSchema,
            'town' => $town,
            ));
        $this->assertNotNull($checkEntity);
    }



}