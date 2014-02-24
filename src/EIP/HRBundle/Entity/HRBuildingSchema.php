<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * \class HRBuildingSchema
 * \brief Building schema entity, all building and building queue refers to these
 *
 * \cond @ORM\Table(name="buildings_schemas") \endcond
 * \cond @ORM\Entity \endcond
 * \cond @UniqueEntity(fields="rValue", message="rValue must be unique") \endcond
 */
class HRBuildingSchema implements \JsonSerializable
{

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the building schema*/

    /** \cond @ORM\Column(name="name", type="string", length=60, nullable=false)  \endcond */
    protected $name; /**< name of the building schema*/

    /** \cond @ORM\Column(name="buildTime", type="integer")  \endcond */
    protected $buildingTime; /**< building time of the building schema*/

    /** \cond @ORM\Column(name="waterCost", type="integer")  \endcond */
    protected $waterCost; /**< water cost of the building schema*/

    /** \cond @ORM\Column(name="pureWaterCost", type="integer")  \endcond */
    protected $pureWaterCost; /**< pure water cost of the building schema*/

    /** \cond @ORM\Column(name="steelCost", type="integer")  \endcond */
    protected $steelCost; /**< steel cost of the building schema*/

    /** \cond @ORM\Column(name="fuelCost", type="integer")  \endcond */
    protected $fuelCost; /**< fuel cost of the building schema*/

    /** \cond @ORM\Column(name="waterCollectRate", type="integer")  \endcond */
    protected $waterCollectRate; /**< water collect rate of the building schema*/

    /** \cond @ORM\Column(name="pureWaterCollectRate", type="integer")  \endcond */
    protected $pureWaterCollectRate; /**< pure water collect rate of the building schema*/

    /** \cond @ORM\Column(name="steelCollectRate", type="integer")  \endcond */
    protected $steelCollectRate; /**< steel collect rate of the building schema*/

    /** \cond @ORM\Column(name="fuelCollectRate", type="integer")  \endcond */
    protected $fuelCollectRate; /**< fuel collect rate of the building schema*/

    /** \cond @ORM\Column(name="rValue", type="integer")  \endcond */
    protected $rValue; /**< value of the building schema*/

    /** \cond @ORM\Column(name="buildingRequirement", type="integer")  \endcond */
    protected $buildingRequirement; /**< value of the building requirement of the building schema*/

    /** \cond @ORM\Column(name="technologyRequirement", type="integer")  \endcond */
    protected $technologyRequirement; /**< value of the technology requirement of the building schema*/

    /** @ORM\Column(type="string", length=32) */
    protected $description;

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
	 *
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
    public function setBuildingTime($buildTime)
    {
        $this->buildingTime = $buildTime;
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
	 *
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
	 *
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
	 *
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
     * Set waterCollectRate
     *
     * @param integer $waterCollectRate
	 *
     * @return HRBuildingSchema
     */
    public function setWaterCollectRate($waterCollectRate)
    {
        $this->waterCollectRate = $waterCollectRate;

        return $this;
    }

    /**
     * Get waterCollectRate
     *
     * @return integer
     */
    public function getWaterCollectRate()
    {
        return $this->waterCollectRate;
    }

    /**
     * Set pureWaterCollectRate
     *
     * @param integer $pureWaterCollectRate
	 *
     * @return HRBuildingSchema
     */
    public function setPureWaterCollectRate($pureWaterCollectRate)
    {
        $this->pureWaterCollectRate = $pureWaterCollectRate;

        return $this;
    }

    /**
     * Get pureWaterCollectRate
     *
     * @return integer
     */
    public function getPureWaterCollectRate()
    {
        return $this->pureWaterCollectRate;
    }

    /**
     * Set steelCollectRate
     *
     * @param integer $steelCollectRate
	 *
     * @return HRBuildingSchema
     */
    public function setSteelCollectRate($steelCollectRate)
    {
        $this->steelCollectRate = $steelCollectRate;

        return $this;
    }

    /**
     * Get steelCollectRate
     *
     * @return integer
     */
    public function getSteelCollectRate()
    {
        return $this->steelCollectRate;
    }

    public function __construct() {
        $this->pureWaterCollectRate = 0;
        $this->waterCollectRate = 0;
        $this->steelCollectRate = 0;
        $this->fuelCollectRate = 0;

        $this->waterCost = 0;
        $this->pureWaterCost = 0;
        $this->steelCost = 0;
        $this->fuelCost = 0;

        $this->buildingTime = 0;
        $this->description = "";
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
     * Set technologyRequirement
     *
     * @param integer $technologyRequirement
     */
    public function setTechnologyRequirement($technologyRequirement) {
        $this->technologyRequirement = $technologyRequirement;
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
     * Get fuelCollectRate
     *
     * @return integer
     */
    public function getFuelCollectRate() {
        return $this->fuelCollectRate;
    }

    /**
     * Set fuelCollectRate
     *
     * @param integer $fuelCollectRate
     */
    public function setFuelCollectRate($fuelCollectRate) {
        $this->fuelCollectRate = $fuelCollectRate;
    }

    /**
     * Get isCollecting
     *
     * @return integer
     */
    public function isCollecting() {
        return $this->waterCollectRate > 0
                || $this->pureWaterCollectRate > 0
                || $this->steelCollectRate > 0
                || $this->fuelCollectRate > 0;
    }

    /** \brief returns an array of the resources collected by the building each minute
    *   @return array
    */
    public function getCollectRates() {
        return array(
                     'waterGain' => $this->waterCollectRate,
                     'pureWaterGain' => $this->pureWaterCollectRate,
                     'steelGain' => $this->steelCollectRate,
                     'fuelGain' => $this->fuelCollectRate
                     );
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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function jsonSerialize() {
        return array(
            'name' => $this->name,
            'id' => $this->id,
            'rValue' => $this->rValue
        );
    }

}
