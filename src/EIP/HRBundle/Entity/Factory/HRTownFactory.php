<?php

namespace EIP\HRBundle\Entity\Factory;
use Symfony\Component\Yaml\Yaml;

/**
 * \brief A factory for the HRTown entity
 */
class HRTownFactory {

	protected $container;

	public function create($name, $x, $y) {
        $town = new \EIP\HRBundle\Entity\HRTown();
        $town->setName($name);
        $town->setXCoord($x);
        $town->setYCoord($y);
        return $town;
	}

	public function getXFromResourceFile($number) {
        if (!is_numeric($number) || $number < 0)
            throw new InvalidArgumentException("[0;inf[ awaited");
		$path = $this->container->get('kernel')->getRootDir().'/../src/EIP/HRBundle/Resources/towns.yml';
		$allTowns = Yaml::parse(file_get_contents($path));
        $count = count($allTowns);
        if ($number > $count)
            throw new InvalidArgumentException("Number provided is greater than the number of available resources ($count)");
        $townInfos = array_splice($allTowns, 0, $number);
        $towns = array();
        foreach ($townInfos as $info) {
            $town = new \EIP\HRBundle\Entity\HRTown();
            $town->setName($info['name']);
            $town->setXCoord($info['x']);
            $town->setYCoord($info['y']);
            $towns[] = $town;
        }
        return $towns;
	}

	public function setContainer($container) {
		$this->container = $container;
	}

}