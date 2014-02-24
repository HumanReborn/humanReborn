<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * HRNotification
 *
 * @ORM\Table(name="notifications")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRNotificationRepository")
 */
class HRNotification
{

    const INFO = 1;
    const ALERT = 2;
    const SUCCESS = 3;

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
     * @ORM\Column(name="notification_time", type="datetime")
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /** @ORM\Column(name="type", type="integer") */
    protected $type;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    protected $user;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")  \endcond */
    protected $game;

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
     * Set time
     *
     * @param \DateTime $time
     * @return HRNotification
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return HRNotification
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

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    public function getGame() {
        return $this->game;
    }

    public function setGame(HRGame $game) {
        $this->game = $game;
    }

    function __construct($type) {
        $this->type = $type;
        $this->time = new \DateTime('now');
    }
    
    //static
    
    public static function createNotification($type, HRGame $game, HRUser $user, $message) {
        $armyDestroyedNotification = new HRNotification($type);
        $armyDestroyedNotification->setGame($game);
        $armyDestroyedNotification->setUser($user);
        $armyDestroyedNotification->setContent($message);
        return $armyDestroyedNotification;
    }

}
