<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRClanApplication
 *
 * @ORM\Table(name="clan_application")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRClanApplicationRepository")
 */
class HRClanApplication
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")
     * @ORM\Column(name="idUser", type="integer")
     */
    private $idUser;

    /**
     * @var integer
     * @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRClan")
     * @ORM\Column(name="idClan", type="integer")
     */
    private $idClan;

    /**
     * @var integer
     *
     * @ORM\Column(name="pending", type="smallint")
     */
    private $pending;


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
     * Set message
     *
     * @param string $message
     * @return HRClanApplication
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return HRClanApplication
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
    
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set idClan
     *
     * @param integer $idClan
     * @return HRClanApplication
     */
    public function setIdClan($idClan)
    {
        $this->idClan = $idClan;
    
        return $this;
    }

    /**
     * Get idClan
     *
     * @return integer 
     */
    public function getIdClan()
    {
        return $this->idClan;
    }

    /**
     * Set pending
     *
     * @param integer $pending
     * @return HRClanApplication
     */
    public function setPending($pending)
    {
        $this->pending = $pending;
    
        return $this;
    }

    /**
     * Get pending
     *
     * @return integer 
     */
    public function getPending()
    {
        return $this->pending;
    }
}
