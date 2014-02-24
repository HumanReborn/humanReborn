<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRBuffSchema
 *
 * @ORM\Table(name="buffs_schemas")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRBuffSchemaRepository")
 */
class HRBuffSchema
{
    const ATTACK_ALL_TYPE = 11; // global for all the armies of the player the game
    const ARMOR_ALL_TYPE = 12;
    const HEALTH_ALL_TYPE = 13;
    const SPEED_ALL_TYPE = 14;
    const RESOURCES_ALL_TYPE = 100;
    const BUILDING_TIME_TYPE = 200;
    const TRAINING_TIME_TYPE = 300;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /** @ORM\Column(name="name", type="string", length=64) */
    protected $name;

    /** @ORM\Column(name="permanent", type="boolean") */
    protected $permanent;

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
     * Set type
     *
     * @param integer $type
     * @return HRBuffSchema
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return HRBuffSchema
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return HRBuffSchema
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getPermanent() {
        return $this->permanent;
    }

    public function setPermanent($permanent) {
        $this->permanent = $permanent;
    }

    function __construct($permanent = false) {
        $this->permanent = $permanent;
    }

}
