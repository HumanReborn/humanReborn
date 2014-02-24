<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRScore
 * \brief Score entity
 *
 * \cond @ORM\Table(name="scores") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRScoreRepository") \endcond
 */
class HRScore {

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< unique identifier*/

    /** \cond @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRGame") \endcond */
    protected $game;
    /** \cond @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRUser") \endcond */
    protected $winnerUser;
    /** \cond @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRClan") \endcond */
    protected $winnerClan;
    /** \cond  @ORM\Column(type="datetime") \endcond */
    protected $endedOn;


    // getters

    public function getId()
    {
        return $this->id;
    }

    public function getGame()
    {
        return $this->game;
    }

    public function getWinner()
    {
        if ($this->winnerClan)
            return $this->winnerClan;
        else if ($this->winnerUser)
            return $this->winnerUser;
    }

    public function getWinnerUser() 
    {
        return $this->winnerUser;
    }

    public function getWinnerClan() 
    {
        return $this->winnerClan;
    }

    public function getEndedOn()
    {
        return $this->endedOn;
    }


    // setters

    public function setId($v)
    {
        $this->id = $v;
    }

    public function setGame($v)
    {
        $this->game = $v;
    }

    public function setWinner($v)
    {
        if ($v instanceof HRUser)
            $this->winnerUser = $v;
        else if ($v instanceof HRClan)
            $this->winnerClan = $v;
    }
    
    public function setWinnerUser(HRUser $v)
    {
        $this->winnerUser = $v;
    }

    public function setWinnerClan(HRClan $v)
    {
        $this->winnerClan = $v;
    }

    public function setEndedOn($dt)
    {
        $this->endedOn = $dt;
    }

    public function __construct()
    {
        $this->game = null;
        $this->winnerUser = null;
        $this->winnerClan = null;
        $this->endedOn = new \DateTime();
    }
}