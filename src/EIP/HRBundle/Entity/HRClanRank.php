<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EIP\HRBundle\Entity\HRClanRank
 *
 * @ORM\Table(name="clan_rank")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRClanRankRepository")
 */
class HRClanRank
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
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="idClan", type="integer")
     * @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRClan")
     */
    private $idClan;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CanFireMember", type="boolean")
     */
    private $CanFireMember;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CanAcceptNewMember", type="boolean")
     */
    private $CanAcceptNewMember;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CanEditText", type="boolean")
     */
    private $CanEditText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CanDeclareWar", type="boolean")
     */
    private $CanDeclareWar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="CanCreateRank", type="boolean")
     */
    private $CanCreateRank;

    /**
     * @var boolean
     *
     * @ORM\Column(name="canDeleteClan", type="boolean")
     */
    private $canDeleteClan;
    
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
     * @return HRClanRank
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
     * Set idClan
     *
     * @param integer $idClan
     * @return HRClanRank
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
     * Set CanFireMember
     *
     * @param boolean $canFireMember
     * @return HRClanRank
     */
    public function setCanFireMember($canFireMember)
    {
        $this->CanFireMember = $canFireMember;
    
        return $this;
    }

    /**
     * Get CanFireMember
     *
     * @return boolean 
     */
    public function getCanFireMember()
    {
        return $this->CanFireMember;
    }

    /**
     * Set CanAcceptNewMember
     *
     * @param boolean $canAcceptNewMember
     * @return HRClanRank
     */
    public function setCanAcceptNewMember($canAcceptNewMember)
    {
        $this->CanAcceptNewMember = $canAcceptNewMember;
    
        return $this;
    }

    /**
     * Get CanAcceptNewMember
     *
     * @return boolean 
     */
    public function getCanAcceptNewMember()
    {
        return $this->CanAcceptNewMember;
    }

    /**
     * Set CanEditText
     *
     * @param boolean $canEditText
     * @return HRClanRank
     */
    public function setCanEditText($canEditText)
    {
        $this->CanEditText = $canEditText;
    
        return $this;
    }

    /**
     * Get CanEditText
     *
     * @return boolean 
     */
    public function getCanEditText()
    {
        return $this->CanEditText;
    }

    /**
     * Set CanDeclareWar
     *
     * @param boolean $canDeclareWar
     * @return HRClanRank
     */
    public function setCanDeclareWar($canDeclareWar)
    {
        $this->CanDeclareWar = $canDeclareWar;
    
        return $this;
    }

    /**
     * Get CanDeclareWar
     *
     * @return boolean 
     */
    public function getCanDeclareWar()
    {
        return $this->CanDeclareWar;
    }

    /**
     * Set CanCreateRank
     *
     * @param boolean $canCreateRank
     * @return HRClanRank
     */
    public function setCanCreateRank($canCreateRank)
    {
        $this->CanCreateRank = $canCreateRank;
    
        return $this;
    }

    /**
     * Get CanCreateRank
     *
     * @return boolean 
     */
    public function getCanCreateRank()
    {
        return $this->CanCreateRank;
    }
    
    public function __construct($idClan, $name, $canFire, $canAccept, $canText, $canWar, $canRank, $canDeleteClan){
        $this->name = $name;
        $this->CanAcceptNewMember = $canAccept;
        $this->CanCreateRank = $canRank;
        $this->CanDeclareWar = $canWar;
        $this->CanEditText = $canText;
        $this->idClan = $idClan;
        $this->CanFireMember = $canFire;
        $this->canDeleteClan = $canDeleteClan;
    }


    /**
     * Set canDeleteClan
     *
     * @param boolean $canDeleteClan
     * @return HRClanRank
     */
    public function setCanDeleteClan($canDeleteClan)
    {
        $this->canDeleteClan = $canDeleteClan;
    
        return $this;
    }

    /**
     * Get canDeleteClan
     *
     * @return boolean 
     */
    public function getCanDeleteClan()
    {
        return $this->canDeleteClan;
    }
}