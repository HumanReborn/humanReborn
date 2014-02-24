<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * \class HRTechnologySchema
 * \brief Technology schema entity. It describe a technology : its name cost, build time, requirement and effect
 *
 * \cond @ORM\Table(name="technologies_schemas") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRTechnologySchemaRepository") \endcond
 * \cond @UniqueEntity(fields="rValue", message="rValue must be unique") \endcond
 */
class HRTechnologySchema implements \JsonSerializable
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the technology schema*/

    /** \cond @ORM\Column(name="buildingRequirement", type="integer")  \endcond */
    protected $buildingRequirement; /**< value of the building requirement of the technology schema*/

    /** \cond @ORM\Column(name="technologyRequirement", type="integer")  \endcond */
    protected $technologyRequirement; /**< value of the technology requirement of the technology schema*/

    /** \cond @ORM\Column(name="name", type="string", length=64)  \endcond */
    protected $name; /**< name of the technology schema*/

    /** \cond @ORM\Column(name="buildingTime", type="integer")  \endcond */
    protected $buildingTime; /**< building time of the technology schema*/

    /** \cond @ORM\Column(name="waterCost", type="integer")  \endcond */
    protected $waterCost; /**< water cost of the technology schema*/

    /** \cond @ORM\Column(name="steelCost", type="integer")  \endcond */
    protected $steelCost; /**< steel cost of the technology schema*/

    /** \cond @ORM\Column(name="pureWaterCost", type="integer")  \endcond */
    protected $pureWaterCost; /**< pure water cost of the technology schema*/

     /** \cond @ORM\Column(name="fuelCost", type="integer")  \endcond */
    protected $fuelCost; /**< fuel cost of the technology schema*/

    /** \cond @ORM\Column(name="rValue", type="integer")  \endcond */
    protected $rValue; /**< value of the technology schema*/

    /** \cond @ORM\Column(name="type", type="integer")  \endcond */
    protected $type; /**< value of the type of technology schema*/

    /** @ORM\Column(type="string", length=32) */
    protected $description;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRBuffSchema") */
    protected $buffSchema;

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
     * Set buildingRequirement
     *
     * @param integer $buildingRequirement
     * @return HRTechnologySchema
     */
    public function setBuildingRequirement($buildingRequirement)
    {
        $this->buildingRequirement = $buildingRequirement;

        return $this;
    }

    /**
     * Get buildingRequirement
     *
     * @return integer
     */
    public function getBuildingRequirement()
    {
        return $this->buildingRequirement;
    }

    /**
     * Set technologyRequirement
     *
     * @param integer $technologyRequirement
     * @return HRTechnologySchema
     */
    public function setTechnologyRequirement($technologyRequirement)
    {
        $this->technologyRequirement = $technologyRequirement;

        return $this;
    }

    /**
     * Get technologyRequirement
     *
     * @return integer
     */
    public function getTechnologyRequirement()
    {
        return $this->technologyRequirement;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HRTechnologySchema
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
     * Set buildingTime
     *
     * @param integer $buildingTime
     * @return HRTechnologySchema
     */
    public function setBuildingTime($buildingTime)
    {
        $this->buildingTime = $buildingTime;

        return $this;
    }

    /**
     * Get buildingTime
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
     * @return HRTechnologySchema
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
     * Set steelCost
     *
     * @param integer $steelCost
     * @return HRTechnologySchema
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
     * Set pureWaterCost
     *
     * @param integer $pureWaterCost
     * @return HRTechnologySchema
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
     * Get rValue
     *
     * @return integer
     */
    public function getRValue() {
        return $this->rValue;
    }

    /**
     * Set rValue
     *
     * @param integer $rValue
     */
    public function setRValue($rValue) {
        $this->rValue = $rValue;
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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getBuffSchema() {
        return $this->buffSchema;
    }

    public function setBuffSchema($buffSchema) {
        $this->buffSchema = $buffSchema;
    }

    public function __construct() {
        $this->waterCost = 0;
        $this->pureWaterCost = 0;
        $this->steelCost = 0;
        $this->fuelCost = 0;

        $this->buildingTime = 0;
        $this->description = "";
    }


     /**
     * Get technologyRequirement
     *
     * @param integer $technoScore
	 *
     * @return integer
     */
    public function checkTechnologyRequirement($technoScore)
    {
        return (($this->technologyRequirement & $technoScore) == $this->technologyRequirement);
    }

    /**
     * Get buildingRequirement
     *
     * @param integer $buildingScore
	 *
     * @return integer
     */
    public function checkBuildingRequirement($buildingScore)
    {
        return (($this->buildingRequirement & $buildingScore) == $this->buildingRequirement);
    }

    public function jsonSerialize() {
        return array(
            'name' => $this->name,
            'id' => $this->id,
            'rValue' => $this->rValue,
            'type' => $this->type,
            'desc' => $this->description,
            'waterCost' => $this->waterCost,
            'pureWaterCost' => $this->pureWaterCost,
            'steelCost' => $this->steelCost,
            'fuelCost' => $this->fuelCost,
            'buildingTime' => $this->buildingTime
        );
    }

}
