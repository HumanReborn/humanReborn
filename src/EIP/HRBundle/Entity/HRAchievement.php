<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRAchievement
 *
 * @ORM\Table(name="achievements")
 * @ORM\Entity
 */
class HRAchievement
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRAchievementSchema") */
    protected $schema;

    /** @ORM\Column(type="datetime") */
    protected $achievedOn;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser", inversedBy="achievements") */
    protected $user;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function setSchema($schema) {
        $this->schema = $schema;
    }

    public function getAchievedOn() {
        return $this->achievedOn;
    }

    public function setAchievedOn($achievedOn) {
        $this->achievedOn = $achievedOn;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    function __construct() {
        $this->achievedOn = new \DateTime('now');
    }


}
