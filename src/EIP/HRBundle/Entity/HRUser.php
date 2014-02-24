<?php

namespace EIP\HRBundle\Entity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRUser
 * \brief User entity, used for authentication. Used everywhere in the project
 *
 * \cond @ORM\Table(name="users") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRUserRepository") \endcond
 * \cond @UniqueEntity(fields="username", message="signup.username.unique") \endcond
 * \cond @UniqueEntity(fields="email", message="signup.email.unique") \endcond
 */
class HRUser implements UserInterface, \Serializable
{
    const STATUS_CLOSED = -1;
    const STATUS_PENDING = 0;
    const STATUS_CONFIRMED = 1;

    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the user*/

    /**
     * \cond @ORM\Column(name="username", type="string", length=20, unique=true, nullable=false) \endcond
     * \cond @Assert\Length(min="3", max="20") \endcond
     * \cond @Assert\NotBlank() \endcond
     */
    protected $username; /**< name of the user*/

    /** @var string $salt \cond @ORM\Column(name="salt", type="string", length=128, nullable=false)  \endcond */
    protected $salt; /**< salt for the user's password*/

    /**
     * \cond @Assert\Length(min="4", max="128") \endcond
     * \cond @Assert\NotBlank() \endcond
     * \cond @ORM\Column(name="password", type="string", length=256, nullable=false) \endcond
     */
    protected $password; /**< password of the user*/

    /**
     * \cond @ORM\Column(name="email", type="string", length=128, unique=true, nullable=false) \endcond
     * \cond @Assert\Email() \endcond
     * \cond @Assert\NotBlank() \endcond
     */
    protected $email; /**< email of the user*/

    /** \cond @ORM\Column(name="created_on", type="date")  \endcond */
    protected $createdOn; /**<  date of the account creation*/

    /** \cond @ORM\Column(name="last_login", type="datetime")  \endcond */
    protected $lastLogin; /**< date of the user's last login*/

    /** @var smallint $status \cond @ORM\Column(name="status", nullable=false)  \endcond */
    protected $status; /**< status of the user*/

    /** @ORM\Column(name="locale", nullable=false, type="string", length=5) */
    protected $locale; /**< locale of the user */

    /** @ORM\Column(type="array") */
    protected $achievementsProgression;

    /** @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRAchievement", mappedBy="user") */
    protected $achievements;

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
     * Set username
     *
     * @param string $username
     * @return HRUser
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return HRUser
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return HRUser
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return HRUser
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
     * Set lastLogin
     *
     * @param \DateTime $lastLogin
     * @return HRUser
     */
    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @return \DateTime
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt() {
        return $this->salt;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt) {
        $this->salt = $salt;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    public function __construct() {
        $this->createdOn = $this->lastLogin = new \DateTime("now");
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->status = self::STATUS_PENDING;
        $this->locale = 'fr';
        $this->achievementsProgression = array(
            'nbGames' => 0,
            'nbUnitsDestroyed' => 0,
            'nbBuildings' => 0,
            'maxHeroLevel' => 1,
            'nbQuestsCompleted' => 0,
            'nbUnitsRecruited' => 0
        );
        $this->achievements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function eraseCredentials() {}

    /**
     * Get roles
     *
     * @return string[]
     */
    public function getRoles() {
        if ($this->status >= self::STATUS_PENDING)
            return array('ROLE_USER');
        return array();
    }

    public function __toString() {
        return $this->username;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    public function getAchievementsProgression() {
        return $this->achievementsProgression;
    }

    public function getAchievements() {
        return $this->achievements;
    }

    public function setAchievements($achievements) {
        $this->achievements = $achievements;
    }

    // achievements related

    public function setAchievementsProgression($achievementProgression) {
        $this->achievementsProgression = $achievementProgression;
    }

    public function updateAchievementsProgression($key, $value) {
        if (array_key_exists($key, $this->achievementsProgression))
            $this->achievementsProgression[$key] = $value;
    }

    public function addToAchievementsProgression($key, $value) {
        if (array_key_exists($key, $this->achievementsProgression))
            $this->achievementsProgression[$key] += $value;
    }

    public function getAchievementsProgressionFor($key) {
        return $this->achievementsProgression[$key];
    }

    public function hasAchieved($schemaid) {
        foreach ($this->achievements  as $ac) {
            if ($ac->getSchema()->getId() == $schemaid) return true;
        }
        return false;
    }

    public function serialize() {
        return serialize($this->username);
    }

    public function unserialize($serialized) {
        $this->username = unserialize($serialized);
    }

}
