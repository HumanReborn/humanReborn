<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRBuildingQueue
 * \brief Building under construction entity
 *
 * \cond @ORM\Table(name="buildings_queues") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRBuildingQueueRepository") \endcond
 */
class HRBuildingQueue
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the building queue*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    protected $user; /**< HRUser of the building*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRTown", inversedBy="buildingsQueue")  \endcond */
    protected $town; /**< HRTown of the building*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRBuildingSchema")  \endcond */
    protected $schema; /**< HRBuildingSchema of the building*/

     /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")  \endcond */
    protected $game; /**< HRGame of the building*/


    // doctrine doesn't handle timestamp, so bigint ...
    /** \cond @ORM\Column(name="endTime", type="bigint", nullable=false)  \endcond */
    protected $endTime; /**< end time of the building in the queue*/

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
     * Get user
     *
     * @return HRUser
     */
    public function getUser() {
        return $this->user;
    }

    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    /**
     * Get game
     *
     * @return HRGame
     */
    public function getGame() {
        return $this->game;
    }

    /**
     * Set game
     *
     * @param HRGame $game
     */
    public function setGame(HRGame $game) {
        $this->game = $game;
    }

    /**
     * Get endTime
     *
     * @return bigint
     */
    public function getEndTime() {
        return $this->endTime;
    }

    /**
     * Set endTime
     *
     * @param bigint $endTime
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    /**
     * Get schema
     *
     * @return HRBuildingSchema
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param HRBuildingSchema $schema
     */
    public function setSchema(HRBuildingSchema $schema) {
        $this->schema = $schema;
    }

    /**
     * Get town
     *
     * @return HRTown
     */
    public function getTown() {
        return $this->town;
    }

    /**
     * Set town
     *
     * @param HRTown $town
     */
    public function setTown(HRTown $town) {
        $this->town = $town;
    }


    /**
     * @param HRBuildingSchema $schema
     * @param HRUser $user
     * @param HRGame $game
     * @param HRTown $town
     * @param bigint $startTime
     */
    public function __construct(HRBuildingSchema $schema, HRUser $user, HRGame $game, HRTown $town,
                                            $startTime)
    {
        $this->schema = $schema;
        $this->user = $user;
        $this->endTime = $startTime + $schema->getBuildingTime();
        $this->game = $game;
        $this->town = $town;
    }

    /**
     * \brief Modifies $endtime according to the given $timeReduction
     * @param integer $timeReduction
     */
    public function applyBuildingTimeReduction($timeReduction) {
        $this->endTime -= $this->schema->getBuildingTime() * $timeReduction / 100.0;
    }

}
