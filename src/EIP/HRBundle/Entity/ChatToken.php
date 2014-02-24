<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class ChatToken
 * \brief ChatTokens are used to identify the user in the in-game messenger 
 *
 * \cond @ORM\Table(name="chat_tokens") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\ChatTokenRepository") \endcond
 */
class ChatToken
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    protected $user; /**< HRUser object */

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")  \endcond */
    protected $game; /**< HRGame object */

    /** \cond @ORM\Column(name="value", type="string", nullable=false, length=256)  \endcond */
    protected $value; /**< unique string value */

    /** \cond @ORM\Column(name="valid_until", type="bigint", nullable=false)  \endcond */
    protected $validUntil; /**< timestamp validUntil */


    /**
     * Get value
     *
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * Get validUntil
     *
     * @return bigint
     */
    public function getValidUntil() {
        return $this->validUntil;
    }

    /**
     * Set validUntil
     *
     * @param bigint $validUntil
     */
    public function setValidUntil($validUntil) {
        $this->validUntil = $validUntil;
    }

    /**
     * Get user
     *
     * @return HRUser
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param HRUser $user
     */
    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    /**
     * Get game
     *
     * @return HRGame
     */
    public function getGame() {
        return $this->game;
    }

    /**
     * Set game
     *
     * @param HRGame $game
     */
    public function setGame(HRGame $game) {
        $this->game = $game;
    }

    /**
     * @param HRUser $user
     * @param HRGame $game
     */
    public function __construct(HRUser $user, HRGame $game)
    {
        $this->user = $user;
        $this->game = $game;
        $this->value = substr(md5(rand()), 0, 256);
        $this->validUntil = time() + 60; // valid for one minute
    }

}
