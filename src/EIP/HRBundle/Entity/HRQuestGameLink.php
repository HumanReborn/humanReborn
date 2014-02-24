<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \brief repository for hrquestgamelink entities
 *
 * @ORM\Table(name="quest_game_link")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRQuestGameLinkRepository")
 */
class HRQuestGameLink
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame") */
    protected $game;
    
    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRQuestSchema") */
    protected $questSchema;

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
     * Set available
     *
     * @param boolean $available
     * @return HRQuestGameLink
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    
        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
    }

    public function getGame() {
        return $this->game;
    }

    public function setGame($game) {
        $this->game = $game;
    }

    public function getQuestSchema() {
        return $this->questSchema;
    }

    public function setQuestSchema($questSchema) {
        $this->questSchema = $questSchema;
    }

    function __construct(HRQuestSchema $questSchema, HRGame $game) {
        $this->game = $game;
        $this->questSchema = $questSchema;
    }

}
