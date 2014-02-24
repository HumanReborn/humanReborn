<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \class HRMessage
 * \brief Message entity
 *
 * \cond @ORM\Table(name="messages") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRMessageRepository") \endcond
 */
class HRMessage
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the message*/

    /**
     * \cond @Assert\NotBlank \endcond
     * \cond @Assert\Length(min="1", max="64") \endcond
     * \cond @ORM\Column(name="title", type="string", length=64) \endcond
     */
    private $title; /**< title of the message*/

    /** \cond @ORM\Column(name="content", type="string", length=1024)  \endcond */
    private $content; /**< content of the message*/

    /** \cond @ORM\Column(name="sentOn", type="datetime")  \endcond */
    private $sentOn; /**< date when the message was sent*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    private $sender; /**< HRUser of the sender*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    private $receiver; /**< HRUser of the receiver*/

    /** \cond @ORM\Column(name="is_read", type="boolean")  \endcond */
    private $read; /**< boolean which represents if the message has been read or not*/

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
     * @return HRMessage
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

    /**
     * Set content
     *
     * @param string $content
     * @return HRMessage
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
     * Set sentOn
     *
     * @param \DateTime $sentOn
     * @return HRMessage
     */
    public function setSentOn($sentOn)
    {
        $this->sentOn = $sentOn;

        return $this;
    }

    /**
     * Get sentOn
     *
     * @return \DateTime
     */
    public function getSentOn()
    {
        return $this->sentOn;
    }

    /**
     * Get sender
     *
     * @return HRUser
     */
    public function getSender() {
        return $this->sender;
    }

    /**
     * Set sender
     *
     * @param HRUser $sender
     */
    public function setSender(HRUser $sender) {
        $this->sender = $sender;
    }

    /**
     * Get receiver
     *
     * @return HRUser
     */
    public function getReceiver() {
        return $this->receiver;
    }

    /**
     * Set receiver
     *
     * @param HRUser $receiver
     */
    public function setReceiver(HRUser $receiver) {
        $this->receiver = $receiver;
    }

    public function __construct() {
        $this->sentOn = new \DateTime("now");
        $this->read = false;
    }

    /**
     * Get read
     *
     * @return boolean
     */
    public function getRead() {
        return $this->read;
    }

    /**
     * Set read
     *
     * @param boolean $read
     */
    public function setRead($read) {
        $this->read = $read;
    }

}
