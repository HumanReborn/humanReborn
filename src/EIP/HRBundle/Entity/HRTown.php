<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \class HRTown
 * \brief Town entity
 *
 * \cond @ORM\Table(name="towns") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRTownRepository") \endcond
 */
class HRTown
{

    const SLOT_PER_TOWN = 10;

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the town*/

    /**
     * \cond @ORM\Column(name="name", type="string", length=50, nullable=false) \endcond
     * \cond @Assert\NotBlank() \endcond
     */
    private $name; /**< name of the town*/

    /**
     * \cond @ORM\Column(name="xCoord", type="integer", nullable=false) \endcond
     * \cond @Assert\NotNull() \endcond
     */
    private $xCoord; /**< x coordinate of the town*/

    /**
     * \cond @ORM\Column(name="yCoord", type="integer", nullable=false) \endcond
     * \cond @Assert\NotNull() \endcond
     */
    private $yCoord; /**< y coordinate of the town*/

    /**
     * \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser") \endcond
     * \cond @Assert\NotNull() \endcond
     */
    protected $owner; /**< HRUser which the town belongs to*/

    /**
     * \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame") \endcond
     * \cond @Assert\NotNull() \endcond
     */
    protected $game; /**< HRGame which the town belongs to*/

    /** \cond @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRArmy", mappedBy="town") \endcond */
    protected $armies; /**<  HRArmies belonging to the town */

    /** \cond @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRBuilding", mappedBy="town") \endcond */
    protected $buildings; /**<  HRBuildings belonging to the town */

    /** \cond @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRBuildingQueue", mappedBy="town") \endcond */
    protected $buildingsQueue; /**<  HRBuildingQueue belonging to the town */

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setXCoord($xCoord)
    {
        $this->xCoord = $xCoord;

        return $this;
    }

    public function getXCoord()
    {
        return $this->xCoord;
    }

    public function setYCoord($yCoord)
    {
        $this->yCoord = $yCoord;

        return $this;
    }

    public function getYCoord()
    {
        return $this->yCoord;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner(HRUser $owner) {
        $this->owner = $owner;
    }

    public function getGame() {
        return $this->game;
    }

    public function setGame(HRGame $game) {
        $this->game = $game;
    }

    public function getArmies() {
        return $this->armies;
    }

    public function setArmies($armies) {
        $this->armies = $armies;
    }

    public function getBuildings() {
        return $this->buildings;
    }

    public function setBuildings($buildings) {
        $this->buildings = $buildings;
    }

    public function getBuildingsQueue() {
        return $this->buildingsQueue;
    }

    public function setBuildingsQueue($buildingsQueue) {
        $this->buildingsQueue = $buildingsQueue;
    }


    function __construct() {
        $this->armies = new \Doctrine\Common\Collections\ArrayCollection();
        $this->buildings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->buildingsQueue = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
    * \brief returns the number of free slot in a town
    * @return integer $freeSlots
    */
    public function getFreeSlotsNumber() {
        return self::SLOT_PER_TOWN - $this->buildings->count() - $this->buildingsQueue->count();
    }

    /**
    * \brief returns an array containing the quantity of resource produced each minute by the town
    * @return array()
    */
    public function getProducedResourcesArray()
    {
        $townProduction = array(
                                'waterGain' => 0.0,
                                'pureWaterGain' => 0.0,
                                'steelGain' => 0.0,
                                'fuelGain' => 0.0
                                );
        foreach ($this->buildings as $b)
        {
            $schema = $b->getSchema();
            $townProduction['waterGain'] += $schema->getWaterCollectRate();
            $townProduction['pureWaterGain'] += $schema->getPureWaterCollectRate();
            $townProduction['steelGain'] += $schema->getSteelCollectRate();
            $townProduction['fuelGain'] += $schema->getFuelCollectRate();
        }
        return $townProduction;
    }

}
