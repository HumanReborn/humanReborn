<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRAchievementSchema
 *
 * @ORM\Table(name="achievements_schemas")
 * @ORM\Entity
 */
class HRAchievementSchema
{
    const NB_GAMES = 1;
    const NB_UNITS_RECRUITED = 2;
    const NB_BUILDINGS = 3;
    const HERO_MAX_LEVEL = 4;
    const NB_QUEST_COMPLETED = 5;
    const NB_UNIT_DESTROYED = 6;


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(type="string", length=64) */
    protected $name;
    /** @ORM\Column(type="integer") */
    protected $type;
    /** @ORM\Column(type="integer") */
    protected $value;

    /** @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRAchievementSchema", mappedBy="prev", cascade={"remove"}) */
    protected $next;
    /** @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRAchievementSchema", inversedBy="next", cascade={"remove"}) */
    protected $prev;

    /** @ORM\Column(type="integer") */
    protected $step;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function getNext() {
        return $this->next;
    }

    public function setNext($next) {
        $this->next = $next;
    }

    public function getPrev() {
        return $this->prev;
    }

    public function setPrev($prev) {
        $this->prev = $prev;
    }

    public function getStep() {
        return $this->step;
    }

    public function setStep($step) {
        $this->step = $step;
    }

    public function isAchieved(HRUser $user)
    {
        $achievementsProgression = $user->getAchievementsProgression();
        switch ($this->type)
        {
            case self::NB_GAMES:
                return $achievementsProgression['nbGames'] >= $this->value;
            case self::NB_UNITS_RECRUITED:
                return $achievementsProgression['nbUnitsRecruited'] >= $this->value;
            case self::NB_BUILDINGS:
                return $achievementsProgression['nbBuildings'] >= $this->value;
            case self::HERO_MAX_LEVEL:
                return $achievementsProgression['maxHeroLevel'] >= $this->value;
            case self::NB_QUEST_COMPLETED:
                return $achievementsProgression['nbQuestsCompleted'] >= $this->value;
            case self::NB_UNIT_DESTROYED:
                return $achievementsProgression['nbUnitsDestroyed'] >= $this->value;
            default: return false;
        }
    }

    public function getDescriptionString()
    {
        switch ($this->type)
        {
            case self::NB_GAMES:
                return 'game.join.desc';
            case self::NB_UNITS_RECRUITED:
                return 'units.recruited.desc';
            case self::NB_BUILDINGS:
                return 'buildings.desc';
            case self::HERO_MAX_LEVEL:
                return 'max.hero.level.desc';
            case self::NB_QUEST_COMPLETED:
                return 'quests.completed.desc';
            case self::NB_UNIT_DESTROYED:
                return 'units.destroyed.desc';
            default: return '';
        }
    }

}
