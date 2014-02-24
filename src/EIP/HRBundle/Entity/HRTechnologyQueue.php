<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EIP\HRBundle\Entity\HRUser;
use EIP\HRBundle\Entity\HRGame;
use EIP\HRBundle\Entity\HRTechnologySchema;

/**
 * \class HRTechnologyQueue
 * \brief Technology being researched, refers to a HRTechnologySchema
 *
 * \cond @ORM\Table(name="technologies_queues") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRTechnologyQueueRepository") \endcond
 */
class HRTechnologyQueue
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the technology queue*/

    /** \cond @ORM\Column(name="endTime", type="bigint", nullable=false)  \endcond */
    private $endTime; /**< end time of the technology in the queue*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRTechnologySchema")  \endcond */
    private $schema; /**< schema of the specified technology*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    private $user; /**< user of the specified technology in this technology queue*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")  \endcond */
    private $game; /**< game of the specified user's technology in this technology queue*/


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

    /**
     * Set user
     *
     * @param HRUser $user
     */
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
     * Get schema
     *
     * @return HRTechnologySchema
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param HRTechnologySchema $schema
     */
    public function setSchema(HRTechnologySchema $schema) {
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
     * Set endtime
     *
     * @param integer $endTime
     */
    public function setEndTime($endTime) {
        $this->endTime = $endTime;
    }

    /**
     * @param HRTechnologySchema $schema
     * @param HRUser $user
     * @param HRGame $game
     * @param integer $startTime
     */
    public function __construct(HRTechnologySchema $schema, HRUser $user, HRGame $game, $startTime)
    {
        $this->schema = $schema;
        $this->user = $user;
        $this->endTime = $startTime + $schema->getBuildingTime();
        $this->game = $game;
    }

}
