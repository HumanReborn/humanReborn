<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * EIP\HRBundle\Entity\HRHero
 *
 * @ORM\Table(name="heroes")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRHeroRepository")
 */
class HRHero
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer $level
     *
     * @ORM\Column(name="level", type="integer")
     */
    protected $level;

    /**
     * \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser") \endcond
     * \cond @Assert\NotNull() \endcond
     */
    protected $user; /**< HRUser which the town belongs to*/

    /**
     *  @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")
     *  @Assert\NotNull()
     */
    protected $game;

    /**
     *  @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRHeroSchema")
     *  @Assert\NotNull()
     */
    protected $schema;

    /**
     *  @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRItem", mappedBy="hero", cascade={"remove"})
     */
    protected $items;

    /** @ORM\Column(type="integer") */
    protected $currentXp;

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
     * Set level
     *
     * @param integer $level
     * @return HRHero
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
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

    public function getSchema() {
        return $this->schema;
    }

    public function setSchema(HRHeroSchema $schema) {
        $this->schema = $schema;
    }

    public function getItems() {
        return $this->items;
    }

    public function setItems(HRItem $items) {
        $this->items = $items;
    }

    function __construct() {
        $this->level = 1;
        $this->currentXp = 1;
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getCurrentXp() {
        return $this->currentXp;
    }

    public function setCurrentXp($currentXp) {
        $this->currentXp = $currentXp;
    }

    /**
     * \brief returns an array containing the required experience for each level
     * @return int[]
     */
    private function getLevelArray() {
        return  array(
            0,
            1000,
            8000,
            20000,
            50000,
            100000,
            250000,
            600000,
            1500000,
            5000000
        );
    }

    /**
     * \fn receiveXp
     * \brief give an amout of experience to the hero ; the hero levels up if he reach the required amount of experience
     * @param integer $receivedXp
     * @return true if the hero has level up, false otherwise
     */
    public function receiveXp($receivedXp) {
        if ($this->level >= 10) return false;
        $levelArray = $this->getLevelArray();
        $this->currentXp += $receivedXp;

        $rvalue = false;
        while (($this->currentXp) >= $levelArray[$this->level])
        {
            $this->level += 1;
            $rvalue = true;
            // update achievements progression
            if ($this->level > $this->user->getAchievementsProgressionFor('maxHeroLevel'))
                $this->user->updateAchievementsProgression('maxHeroLevel', $this->level);
        }
        return $rvalue;
    }

    /**
     * \brief returns the progression of the current level in percents
     * @return float
     */
    public function getLevelProgression() {
        if ($this->level >= 10) return 100;
        $r = $this->getLevelArray();
        $required = $r[$this->level];
        return ($this->currentXp / $required * 100.0);
    }

    /**
     * \brief returns the required xp to lvl up
     * @return integer
     */
    public function getXpToNextLevel() {
        if ($this->level == 10) return 0;
        $r = $this->getLevelArray();
        $required = $r[$this->level];
        return ($required - $this->currentXp);
    }

    /**
     * \brief returns the total attack bonus provided by the hero (base + levels)
     * @return float
     */
    public function getTotalBonusAttack() {
        return $this->schema->getBonusAttack() + ($this->level - 1) * $this->schema->getBonusAttackPerLevel();
    }

    /**
     * \brief returns the total health bonus provided by the hero (base + levels)
     * @return float
     */
    public function getTotalBonusHealth() {
        return $this->schema->getBonusHealth() + ($this->level - 1) * $this->schema->getBonusHealthPerLevel();
    }

    /**
     * \brief returns the total armor bonus provided by the hero (base + levels)
     * @return float
     */
    public function getTotalBonusArmor() {
        return $this->schema->getBonusArmor() + ($this->level - 1) * $this->schema->getBonusArmorPerLevel();
    }

    /**
     * \brief returns the total speed bonus provided by the hero (base + levels)
     * @return float
     */
    public function getTotalBonusSpeed() {
        return $this->schema->getBonusSpeed() + ($this->level - 1) * $this->schema->getBonusSpeedPerLevel();
    }

    /**
     * \brief returns the total collect bonus provided by the hero (base + levels)
     * @return float
     */
    public function getTotalBonusCollectRate() {
        return $this->schema->getBonusCollectRate() + ($this->level - 1) * $this->schema->getBonusCollectRatePerLevel();
    }

}
