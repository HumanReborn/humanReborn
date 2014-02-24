<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EIP\HRBundle\Entity\HRClanMembers
 *
 * @ORM\Table(name="clan_members")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRClanMembersRepository")
 */
class HRClanMembers
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
     * @var integer
     *
     * @ORM\Column(name="idClan", type="integer")
     * @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRClan")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idClan;

    /**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="integer")
     * @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="idRank", type="integer")
     * @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRClanRank")
     */
    private $idRank;

    /**
     * Set id
     *
     * @param integer $id
     * @return HRClanMembers
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
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
     * Set idClan
     *
     * @param integer $idClan
     * @return HRClanMembers
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
     * Set idUser
     *
     * @param integer $idUser
     * @return HRClanMembers
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
     * Set idRank
     *
     * @param integer $idRank
     * @return HRClanMembers
     */
    public function setIdRank($idRank)
    {
        $this->idRank = $idRank;
    
        return $this;
    }

    /**
     * Get idRank
     *
     * @return integer 
     */
    public function getIdRank()
    {
        return $this->idRank;
    }
    
   public function __construct($idClan, $idUser, $idRank){
        $this->idClan = $idClan;
        $this->idRank = $idRank;
        $this->idUser = $idUser;
    }
}