<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRTechnology
 * \brief Technology entity, refers to a HRTechnologySchema
 *
 * \cond @ORM\Table(name="technologies") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRTechnologyRepository") \endcond
 */
class HRTechnology
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the technology*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRTechnologySchema")  \endcond */
    private $schema; /**< schema of the technology*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")    \endcond */
    private $game; /**< game associated with this technology*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    private $user; /**< user associated with this technology */


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

}
