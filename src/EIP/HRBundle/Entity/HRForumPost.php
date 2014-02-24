<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRForumPost
 *
 * @ORM\Table(name="posts")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRForumPostRepository")
 */
class HRForumPost
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
     * @ORM\Column(name="content", type="text")
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="postedOn", type="datetime")
     */
    private $postedOn;

     /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  */
    protected $user;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRForumTopic", inversedBy="posts")   */
    protected $topic;

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
     * Set content
     *
     * @param string $content
     * @return HRForumPost
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set postedOn
     *
     * @param \DateTime $postedOn
     * @return HRForumPost
     */
    public function setPostedOn($postedOn)
    {
        $this->postedOn = $postedOn;

        return $this;
    }

    /**
     * Get postedOn
     *
     * @return \DateTime
     */
    public function getPostedOn()
    {
        return $this->postedOn;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    public function getTopic() {
        return $this->topic;
    }

    public function setTopic($topic) {
        $this->topic = $topic;
    }

    public function __construct() {
        $this->postedOn = new \DateTime();
    }


}
