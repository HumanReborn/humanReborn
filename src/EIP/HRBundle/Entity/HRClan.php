<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EIP\HRBundle\Entity\HRGame;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EIP\HRBundle\Entity\HRClan
 *
 * @ORM\Table(name="clans")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRClanRepository")
 */
class HRClan
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=30, nullable=false)
     * @Assert\Length(min="3", max="30")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string $acronym
     *
     * @ORM\Column(name="acronym", type="string", length=4, nullable=false)
     * @Assert\Length(min="2", max="4")
     * @Assert\NotBlank()
     */
    private $acronym;

    /**
     * @var string $banner
     *
     * @ORM\Column(name="banner", type="string", length=64, nullable=true)
     */
    private $banner;

    /**
     * @var text privatePresentation
     *
     * @ORM\Column(name="privatePresentation", type="text", nullable=true)
     */
    private $privatePresentation;

    /**
     * @var text publicPresentation
     *
     * @ORM\Column(name="publicPresentation", type="text", nullable=true)
     */
    private $publicPresentation;

    /**
     *
     * @var type boolean $recruitmentStatut
     *
     * @ORM\Column(name="recruitmentStatut", type="boolean")
     */
    private $recruitmentStatut;

    /**
     * @var \DateTime $createdOn
     *
     * @ORM\Column(name="createdOn", type="date", nullable=false)
     */
    private $createdOn;

    /**
     * @var integer idGame
     *
     * @ORM\Column(name="idGame", type="integer")
     */
    private $idGame;

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
     * Set id
     *
     * @param integer $id
     * @return HRClan
     */
    public function setId($id)
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HRClan
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return HRClan
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    public function __construct() {
        $this->createdOn = new \DateTime("now");
    }

    /**
     * Set idGame
     *
     * @param integer $idGame
     * @return HRClan
     */
    public function setIdGame($idGame)
    {
        $this->idGame = $idGame;

        return $this;
    }

    /**
     * Get idGame
     *
     * @return integer
     */
    public function getIdGame()
    {
        return $this->idGame;
    }

    /**
     * Set recruitmentStatut
     *
     * @param boolean $recruitmentStatut
     * @return HRClan
     */
    public function setRecruitmentStatut($recruitmentStatut)
    {
        $this->recruitmentStatut = $recruitmentStatut;

        return $this;
    }

    /**
     * Get recruitmentStatut
     *
     * @return boolean
     */
    public function getRecruitmentStatut()
    {
        return $this->recruitmentStatut;
    }


    /**
     * Set banner
     *
     * @param string $banner
     * @return HRClan
     */
    public function setBanner($banner)
    {
        $this->banner = $banner;

        return $this;
    }

    /**
     * Get banner
     *
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Set privatePresentation
     *
     * @param string $privatePresentation
     * @return HRClan
     */
    public function setPrivatePresentation($privatePresentation)
    {
        $this->privatePresentation = $privatePresentation;

        return $this;
    }

    /**
     * Get privatePresentation
     *
     * @return string
     */
    public function getPrivatePresentation()
    {
        return $this->privatePresentation;
    }

    /**
     * Set publicPresentation
     *
     * @param string $publicPresentation
     * @return HRClan
     */
    public function setPublicPresentation($publicPresentation)
    {
        $this->publicPresentation = $publicPresentation;

        return $this;
    }

    /**
     * Get publicPresentation
     *
     * @return string
     */
    public function getPublicPresentation()
    {
        return $this->publicPresentation;
    }

    /**
     * Set acronym
     *
     * @param string $acronym
     * @return HRClan
     */
    public function setAcronym($acronym)
    {
        $this->acronym = $acronym;

        return $this;
    }

    /**
     * Get acronym
     *
     * @return string
     */
    public function getAcronym()
    {
        return $this->acronym;
    }
}