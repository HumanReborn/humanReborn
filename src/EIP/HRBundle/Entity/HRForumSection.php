<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRForumSection
 *
 * @ORM\Table(name="sections")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRForumSectionRepository")
 */
class HRForumSection
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
     * @var \DateTime
     *
     * @ORM\Column(name="name", type="string", length=60)
     */
    private $name;

    /** @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRForumTopic", mappedBy="section", cascade={"remove"})  */
    protected $topics;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=256)
     */
    protected $description;

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
     * @param \DateTime $name
     * @return HRForumSection
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return \DateTime
     */
    public function getName()
    {
        return $this->name;
    }

    public function getTopics() {
        return $this->topics;
    }

    public function setTopics(\Doctrine\Common\Collections\ArrayCollection $topics) {
        $this->topics = $topics;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function __construct() {
        $this->topics = new \Doctrine\Common\Collections\ArrayCollection();
    }


}
