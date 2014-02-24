<?php

use EIP\HRBundle\Utils\TestTools;

class PersistTest extends EIP\HRBundle\Utils\ATestCase
{
    private function addEntities($em)
    {
        foreach (array("un", "deux", "trois") as $name)
        {
            $s = new EIP\HRBundle\Entity\HRTechnologySchema();
            $s->setName($name);
            $s->setBuildingRequirement(0);
            $s->setTechnologyRequirement(0);
            $s->setRValue(0);
            $s->setType(1);
            $em->persist($s);
        }
        //$em->flush();
    }
    
    public function testPersist()
    {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $technos = $em->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();
        $this->assertCount(0, $technos);
        $this->addEntities($em);
        $em->flush();
        $re = $em->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();
        $this->assertCount(3, $re);
    }
}