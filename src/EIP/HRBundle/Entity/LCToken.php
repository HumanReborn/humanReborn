<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EIP\HRBundle\Entity\HRUser;

/**
 * \class LCToken
 * \brief Theses tokens are created when a user ask for a password reset
 *
 * \cond @ORM\Table(name="lost_credentials_tokens") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\LCTokenRepository") \endcond
 */
class LCToken
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the lost credential token*/

    /** \cond @ORM\Column(name="expire", type="datetime")  \endcond */
    private $expire; /**< date when the token expire*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    private $user; /**< this token belongs to this $user*/

    /** \cond @ORM\Column(name="lc_key", type="string", length=128, nullable=false)  \endcond */
    private $key; /**< unique string*/

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
     * Set expire
     *
     * @param \DateTime $expire
     * @return LCToken
     */
    public function setExpire($expire)
    {
        $this->expire = $expire;

        return $this;
    }

    /**
     * Get expire
     *
     * @return \DateTime
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * Get user
     * @return HRUser
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Set user
     * @param HRUser $user
     */
    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    /**
     * get key
     * @return string key
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * Set key
     * @param string $key
     */
    public function setKey($key) {
        $this->key = $key;
    }

    public function __construct() {
        $this->key = uniqid(php_uname('n'), true);
        $a = rand() % 300 + 50;
        foreach (range(0, $a) as $i) {
            if (strlen($this->key > 64))
                $this->key = substr($this->key, rand() % 64);
            $this->key = hash("sha512", $this->key);
        }
        $this->expire = new \DateTime("now");
        $this->expire->add(new \DateInterval("PT1H")); // token expires one hour after beeing sent
    }

}
