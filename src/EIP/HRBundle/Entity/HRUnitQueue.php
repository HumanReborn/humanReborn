<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRUnitQueue
 * \brief Unit being trained, refers to HRUnitSchema
 *
 * \cond @ORM\Table(name="units_queue") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRUnitQueueRepository") \endcond
 */
class HRUnitQueue
{
    const MAX_QUEUE_SLOTS = 10;

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the unit*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRArmy")  \endcond */
    protected $army; /**< HRArmy which the unit belongs to*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUnitSchema")  \endcond */
    protected $schema; /**< HRUnitSchema of the unit*/

    /** \cond @ORM\Column(name="endTime", type="bigint", nullable=false)  \endcond */
    protected $endTime; /**< end time of the unit in the queue*/

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
     * Get army
     *
     * @return HRArmy
     */
    public function getArmy() {
        return $this->army;
    }

    /**
     * Set army
     *
     * @param HRArmy $army
     */
    public function setArmy(HRArmy $army) {
        $this->army = $army;
    }

    /**
     * Get schema
     *
     * @return HRUnitSchema
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param HRUnitSchema $schema
     */
    public function setSchema(HRUnitSchema $schema) {
        $this->schema = $schema;
    }

    /**
     * Get endTime
     *
     * @return integer
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * Set endTime
     *
     * @param integer $endTime
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    /**
     * @param HRUnitSchema $schema
     * @param HRArmy $army
     * @param integer $startTime
     */
    public function __construct(HRUnitSchema $schema, HRArmy $army, $startTime)
    {
        $this->schema = $schema;
        $this->endTime = $startTime + $schema->getBuildingTime();
        $this->army = $army;
    }

    /**
     * \brief modifies $endtime according to the time reduction
     * @param integer
     */
    public function applyTrainingTimeReduction($timeReduction) {
        $this->endTime -= $this->schema->getBuildingTime() * $timeReduction / 100.0;
    }
}
