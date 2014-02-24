<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * \class HRGame
 * \brief Game entity
 *
 * \cond @ORM\Table(name="games") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRGameRepository") \endcond
 * \cond @UniqueEntity(fields="name") \endcond
 */
class HRGame
{
    const STATUS_CLOSED = 0;
    const STATUS_OPENED = 1;
    const STATUS_CAN_SIGNUP = 2;

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the game*/

    /**
     * \cond @ORM\Column(name="name", type="string", length=30, nullable=false) \endcond
     * \cond @Assert\NotBlank() \endcond
     */
    protected $name; /**< name of the game*/

    /** \cond @ORM\Column(name="createdOn", type="date")  \endcond */
    protected $createdOn; /**< date when the game was created*/

        /** \cond @ORM\Column(name="openedOn", type="datetime")  \endcond */
    protected $openedOn; /**< date when the game's status switched to opened */

    /** \cond @ORM\Column(name="status", type="smallint")  \endcond */
    protected $status; /**< status of the game (opened, closed, ...)*/

    /** \cond  @ORM\Column(name="private", type="boolean") \endcond */
    protected $private; /**< true if the game is private */

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
     * @return HRGame
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
     * @return HRGame
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

    /**
     * Set status
     *
     * @param integer $status
     * @return HRGame
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Checks if the game is opened
     *
     * @return integer
     */
    public function isOpened() {
        return $this->status == HRGame::STATUS_OPENED;
    }

    /**
     * Checks if the game is closed
     *
     * @return integer
     */
    public function isClosed() {
        return $this->status == HRGame::STATUS_CLOSED;
    }

    /**
     * Checks if users can sign up for the game
     *
     * @return integer
     */
    public function canSignUp() {
        return $this->status == HRGame::STATUS_CAN_SIGNUP;
    }

    public function isPrivate() {
        return $this->private;
    }

    public function getPrivate() {
        return $this->private;
    }

    public function setPrivate($p) {
        $this->private = $p;
    }

    public function getOpenedOn() {
        return $this->openedOn;
    }
    public function setOpenedOn($d) {
        $this->openedOn = $d;
    }

    public function __construct() {
        $this->createdOn = new \DateTime();
        $this->openedOn = new \DateTime();
        $this->status = self::STATUS_CLOSED;
        $this->private = false;
    }

    /*
     * UTILS
     */
    public function __toString() {
        return $this->name;
    }

}
