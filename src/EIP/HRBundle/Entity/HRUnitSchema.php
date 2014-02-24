<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRUnitSchema
 * \brief Unit schema, contains all the informations about a unit: name, cost, buildtime, stats, ...
 *
 * \cond @ORM\Table(name="units_schemas") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRUnitSchemaRepository") \endcond
 */
class HRUnitSchema implements \JsonSerializable
{
    const LIGHT_UNIT = 0;
    const HEAVY_UNIT = 1;
    const ARMORED_UNIT = 2;
    const BEAST_UNIT = 4;

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the unit schema*/

    /** \cond @ORM\Column(name="img", type="integer")  \endcond */
    protected $img; /**< id of the unit's image*/

    /** \cond @ORM\Column(name="name", type="string", length=60, nullable=false)  \endcond */
    protected $name; /**< name of the unit schema*/

    /** \cond @ORM\Column(name="buildTime", type="integer")  \endcond */
    protected $buildingTime; /**< building time of the unit schema*/

    /** \cond @ORM\Column(name="buildingRequirement", type="integer")  \endcond */
    protected $buildingRequirement; /**< value of the building requirement of the unit schema*/

    /** \cond @ORM\Column(name="technologyRequirement", type="integer")  \endcond */
    protected $technologyRequirement; /**< value of the technology requirement of the unit schema*/

    /** \cond @ORM\Column(name="waterCost", type="integer")  \endcond */
    protected $waterCost; /**< water cost of the unit schema*/

    /** \cond @ORM\Column(name="pureWaterCost", type="integer")  \endcond */
    protected $pureWaterCost; /**< pure water cost of the unit schema*/

    /** \cond @ORM\Column(name="steelCost", type="integer")  \endcond */
    protected $steelCost; /**< steel cost of the unit schema*/

    /** \cond @ORM\Column(name="fuelCost", type="integer")  \endcond */
    protected $fuelCost; /**< fuel cost of the unit schema*/

    /** \cond @ORM\Column(name="hp", type="integer")  \endcond */
    protected $hp; /**< health point of the unit schema*/

    /** \cond @ORM\Column(name="attack", type="integer")  \endcond */
    protected $attack; /**< attack of the unit schema*/

    /** \cond @ORM\Column(name="armor", type="integer")  \endcond */
    protected $armor; /**< armor of the unit schema*/

    /** \cond @ORM\Column(name="type", type="integer")  \endcond */
    protected $type; /**< type of the unit schema*/

    /** \cond @ORM\Column(name="speed", type="integer")  \endcond */
    protected $speed; /**< speed of the unit schema*/

    /** \cond @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRUnitDescription", mappedBy="schema")  \endcond */
    protected $description;

     /**
     * Get img
     *
     * @return integer
     */
    public function getImg() {
        return $this->img;
    }

    /**
     * Set img
     *
     * @param integer $img
     */
    public function setImg($img) {
        $this->img = $img;
    }

     /**
     * Get hp
     *
     * @return integer
     */
    public function getHp() {
        return $this->hp;
    }

    /**
     * Set hp
     *
     * @param integer $hp
     */
    public function setHp($hp) {
        $this->hp = $hp;
    }

     /**
     * Get attack
     *
     * @return integer
     */
    public function getAttack() {
        return $this->attack;
    }

    /**
     * Set attack
     *
     * @param integer $attack
     */
    public function setAttack($attack) {
        $this->attack = $attack;
    }

     /**
     * Get armor
     *
     * @return integer
     */
    public function getArmor() {
        return $this->armor;
    }

    /**
     * Set armor
     *
     * @param integer $armor
     */
    public function setArmor($armor) {
        $this->armor = $armor;
    }

     /**
     * Get type
     *
     * @return integer
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param integer $type
     */
    public function setType($type) {
        $this->type = $type;
    }

     /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HRBuildingSchema
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set buildTime
     *
     * @param integer $buildTime
     * @return HRBuildingSchema
     */
    public function setBuildingTime($buildingTime)
    {
        $this->buildingTime = $buildingTime;

        return $this;
    }

    /**
     * Get buildTime
     *
     * @return integer
     */
    public function getBuildingTime()
    {
        return $this->buildingTime;
    }

    /**
     * Set waterCost
     *
     * @param integer $waterCost
     * @return HRBuildingSchema
     */
    public function setWaterCost($waterCost)
    {
        $this->waterCost = $waterCost;

        return $this;
    }

    /**
     * Get waterCost
     *
     * @return integer
     */
    public function getWaterCost()
    {
        return $this->waterCost;
    }

    /**
     * Set pureWaterCost
     *
     * @param integer $pureWaterCost
     * @return HRBuildingSchema
     */
    public function setPureWaterCost($pureWaterCost)
    {
        $this->pureWaterCost = $pureWaterCost;

        return $this;
    }

    /**
     * Get pureWaterCost
     *
     * @return integer
     */
    public function getPureWaterCost()
    {
        return $this->pureWaterCost;
    }

    /**
     * Set steelCost
     *
     * @param integer $steelCost
     * @return HRBuildingSchema
     */
    public function setSteelCost($steelCost)
    {
        $this->steelCost = $steelCost;

        return $this;
    }

    /**
     * Get steelCost
     *
     * @return integer
     */
    public function getSteelCost()
    {
        return $this->steelCost;
    }

    /**
     * Set speed
     *
     * @param integer $speed
     * @return HRUnitSchema
     */
    public function setSpeed($speed)
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * Get speed
     *
     * @return integer
     */
    public function getSpeed()
    {
        return $this->speed;
    }

     /**
     * Get fuelCost
     *
     * @return integer
     */
    public function getFuelCost() {
        return $this->fuelCost;
    }

    /**
     * Set fuelCost
     *
     * @param integer $fuelCost
     */
    public function setFuelCost($fuelCost) {
        $this->fuelCost = $fuelCost;
    }

     /**
     * Get buildingRequirement
     *
     * @return integer
     */
    public function getBuildingRequirement() {
        return $this->buildingRequirement;
    }

    /**
     * Set buildingRequirement
     *
     * @param integer $buildingRequirement
     */
    public function setBuildingRequirement($buildingRequirement) {
        $this->buildingRequirement = $buildingRequirement;
    }

     /**
     * Get technologyRequirement
     *
     * @return integer
     */
    public function getTechnologyRequirement() {
        return $this->technologyRequirement;
    }

    /**
     * Get getDescription
     *
     * @return HRUnitDescription
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set setDescription
     *
     * @param HRUnitDescription $description
     */
    public function setDescription(HRUnitDescription $description) {
        $this->description = $description;
    }

    /**
     * Set technologyRequirement
     *
     * @param integer $technologyRequirement
     */
    public function setTechnologyRequirement($technologyRequirement) {
        $this->technologyRequirement = $technologyRequirement;
    }

    public function __construct() {
        $this->waterCost = 0;
        $this->pureWaterCost = 0;
        $this->steelCost = 0;
        $this->fuelCost = 0;

        $this->buildingTime = 0;
        $this->hp = 0;
        $this->armor = 0;
        $this->attack = 0;
        $this->speed = 0;
        $this->type = 0;
        $this->technologyRequirement = 0;
        $this->buildingRequirement = 0;
    }

    /**
     * Check technologyRequirement
     *
     * @param integer $technoScore
	 * @return boolean
     */
    public function checkTechnologyRequirement($technoScore)
    {
        return (($this->technologyRequirement & $technoScore) == $this->technologyRequirement);
    }

    /**
     * Check buildingRequirement
     *
     * @param integer $buildingScore
	 * @return boolean
     */
    public function checkBuildingRequirement($buildingScore)
    {
        return (($this->buildingRequirement & $buildingScore) == $this->buildingRequirement);
    }

    /**
    * \brief custom serialization rules
    */
    public function jsonSerialize() {
        return array(
            'name' => $this->name,
            'img' => $this->img,
            'id' => $this->id,
            'waterCost' => $this->waterCost,
            'pureWaterCost' => $this->pureWaterCost,
            'steelCost' => $this->steelCost,
            'fuelCost' => $this->fuelCost,
            'buildingTime' => $this->buildingTime,
            'hp' => $this->hp,
            'attack' => $this->attack,
            'armor' => $this->armor,
            'speed' => $this->speed,
            'type' => $this->type
        );
    }
}
