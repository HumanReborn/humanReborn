<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRForumTopic
 *
 * @ORM\Table(name="topics")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRForumTopicRepository")
 */
class HRForumTopic
{
    const NO_CONSTRAINT = 0;
    const CLAN_READ_CONSTRAINT = 1;
    const CLAN_WRITE_CONSTRAINT = 2;
    const ALL_WRITE_CONSTRAINT = 4;
    const ALL_READ_CONSTRAINT = 8;


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
     * @ORM\Column(name="title", type="string", length=80)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastPostOn", type="datetime")
     */
    private $lastPostOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sticky", type="boolean")
     */
    private $sticky;


    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRForumSection", inversedBy="topics")   */
    protected $section;

    /** @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRForumPost", mappedBy="topic", cascade={"remove"})  */
    protected $posts;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  */
    protected $user;

    /**
     *
     * @var integer
     * @ORM\Column(name="constraints", type="integer")
     */
    protected $constraints;

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
     * Set title
     *
     * @param string $title
     * @return HRForumTopic
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function getLastPostOn() {
        return $this->lastPostOn;
    }

    public function setLastPostOn(\DateTime $lastPostOn) {
        $this->lastPostOn = $lastPostOn;
    }

    public function getConstraints() {
        return $this->constraints;
    }

    public function setConstraints($constraints) {
        $this->constraints = $constraints;
    }

    /**
     * Set sticky
     *
     * @param boolean $sticky
     * @return HRForumTopic
     */
    public function setSticky($sticky)
    {
        $this->sticky = $sticky;

        return $this;
    }

    /**
     * Get sticky
     *
     * @return boolean
     */
    public function getSticky()
    {
        return $this->sticky;
    }

    public function getSection() {
        return $this->section;
    }

    public function setSection(HRForumSection $section) {
        $this->section = $section;
    }

    public function getPosts() {
        return $this->posts;
    }

    public function setPosts(\Doctrine\Common\Collections\ArrayCollection $posts) {
        $this->posts = $posts;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    public function __construct() {
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lastPostOn = new \DateTime();
        $this->constraints = HRForumTopic::NO_CONSTRAINT;
    }

    public function userCanPost($user, \Doctrine\ORM\EntityRepository $rep)
    {
        if ($this->constraints == HRForumTopic::NO_CONSTRAINT) return true;
        if ($this->constraints & HRForumTopic::ALL_WRITE_CONSTRAINT) return false;
        if ($this->constraints & HRForumTopic::CLAN_WRITE_CONSTRAINT)
        {
            // One clan per game ???
            return false;
        }
    }

    public function userCanRead($user, \Doctrine\ORM\EntityRepository $rep)
    {
        if ($this->constraints == HRForumTopic::NO_CONSTRAINT) return true;
        if ($this->constraints & HRForumTopic::ALL_READ_CONSTRAINT) return false;
        if ($this->constraints & HRForumTopic::CLAN_READ_CONSTRAINT)
        {
            return false;
        }
    }

}
