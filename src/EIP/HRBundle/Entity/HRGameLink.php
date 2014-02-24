<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EIP\HRBundle\Entity\HRGame;
use EIP\HRBundle\Entity\HRUser;

/**
 * \class HRGameLink
 * \brief Links a HRUser and a HRGame, store the resources of the player for the game, and several other informations
 *
 * \cond @ORM\Table(name="user_game_link") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRGameLinkRepository") \endcond
 */
class HRGameLink
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the game link*/

    /** \cond @ORM\Column(name="joinedOn", type="date")	 \endcond */
    protected $joinedOn; /**< date when the user joined the game*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")    \endcond */
    protected $game;  /**< game joined by the user*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    protected $user; /**< user which joined the game*/

    /** \cond @ORM\Column(name="resources", type="array")  \endcond */
    protected $resources; /**< user's resources in the game*/

    /**  \cond @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRBuff", mappedBy="gameLink") \endcond */
    protected $buffs;

    /** \cond @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRHero") \endcond */
    protected $hero;

    /**  @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRQuest", mappedBy="gamelink")  */
    protected $quests;

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
     * Set joinedOn
     *
     * @param \DateTime $joinedOn
     * @return HRGameLink
     */
    public function setJoinedOn($joinedOn)
    {
        $this->joinedOn = $joinedOn;

        return $this;
    }

    /**
     * Get joinedOn
     *
     * @return \DateTime
     */
    public function getJoinedOn()
    {
        return $this->joinedOn;
    }

    /**
     * Set game
     *
     * @param HRGame $game
     */
    public function setGame(HRGame $game) {
        $this->game = $game;
        $this->joinedOn = new \DateTime("now");
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
     * Set user
     *
     * @param HRUser $user
     */
    public function setUser(HRUser $user) {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return HRUser
     */
    public function getUser() {
        return $this->user;
    }

    public function getBuffs() {
        return $this->buffs;
    }

    public function setBuffs($buffs) {
        $this->buffs = $buffs;
    }

    public function getHero() {
        return $this->hero;
    }

    public function setHero(HRHero $hero) {
        $this->hero = $hero;
    }

    public function __construct() {
        $this->joinedOn = new \DateTime('now');
        $this->resources['water'] = 0.0;
        $this->resources['waterStock'] = 0;
        $this->resources['waterGain'] = 0;
        $this->resources['pureWater'] = 0.0;
        $this->resources['pureWaterStock'] = 0;
        $this->resources['pureWaterGain'] = 0;
        $this->resources['steel'] = 0.0;
        $this->resources['steelStock'] = 0;
        $this->resources['steelGain'] = 0;
        $this->resources['fuel'] = 0.0;
        $this->resources['fuelStock'] = 0;
        $this->resources['fuelGain'] = 0;
        $this->buffs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->quests = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getQuests() {
        return $this->quests;
    }

    /**
     * Get resources
     *
     * @return integer[]
     */
    public function getResources() {
        return $this->resources;
    }

    /**
     * Set resources
     *
     * @param integer[]
     */
    public function setResources($resources) {
        $this->resources = $resources;
    }

    /**
     * Get canBuy
     *
     * @param HRSchema $schema
     * @return boolean
     */
    public function canBuy($schema, $quantity = 1.0)
    {
        return (
                ($schema->getWaterCost() * $quantity) <= $this->resources['water']
                && ($schema->getPureWaterCost() * $quantity) <= $this->resources['pureWater']
                && ($schema->getSteelCost() * $quantity) <= $this->resources['steel']
                && ($schema->getFuelCost() * $quantity) <= $this->resources['fuel']
                );
    }

    /**
     * Set buy
     *
     * @param HRSchema $schema
     */
    public function Buy($schema, $quantity = 1.0)
    {
        $this->resources['water'] -= ($schema->getWaterCost() * $quantity);
        $this->resources['pureWater'] -= ($schema->getPureWaterCost() * $quantity);
        $this->resources['steel'] -= ($schema->getSteelCost() * $quantity);
        $this->resources['fuel'] -= ($schema->getFuelCost() * $quantity);
    }

    /**
     * Set addCollectingBuilding
     *
     * @param HRSchema $schema
     */
    public function addCollectingBuilding(HRBuildingSchema $schema)
    {
        $this->resources['waterGain'] += $schema->getWaterCollectRate();
        $this->resources['pureWaterGain'] += $schema->getPureWaterCollectRate();
        $this->resources['steelGain'] += $schema->getSteelCollectRate();
        $this->resources['fuelGain'] += $schema->getFuelCollectRate();
    }

    /**
     * \brief add resources, buff and hero bonuses are taken into consideration
     * @param timestamp $currentTime
     */
    public function updateResources($currentTime) {
        // get valid bonus
        $bonus = 100.0;
        foreach ($this->buffs as $b) {
            if (($b->getSchema()->getPermanent() || $currentTime <= $b->getValidUntil()) && $b->getSchema()->getType() == HRBuffSchema::RESOURCES_ALL_TYPE)
                $bonus += $b->getSchema()->getValue();
        }
        $bonus += $this->hero->getSchema()->getBonusCollectRate() + ($this->hero->getSchema()->getBonusCollectRatePerLevel() * $this->hero->getLevel());
        $bonus /= 100.0;
        // add the resources
        $rtypes = array('water', 'pureWater', 'fuel', 'steel');
        foreach ($rtypes as $type)
        {
            $this->resources[$type] += $this->resources[$type.'Gain'] * $bonus;
            if ($this->resources[$type] > $this->resources[$type.'Stock'])
                    $this->resources[$type] = $this->resources[$type.'Stock'];
        }
    }

    /**
     * \brief return a copy of the resources array where the gain is updated by buff and hero bonuses
     * @param timestamp $currentTime
     * @return array
     */
    public function getBuffedResources($currentTime) {
        $r = $this->resources;
        $bonus = 100.0;
        foreach ($this->buffs as $b) {
            if (($b->getSchema()->getPermanent() || $currentTime <= $b->getValidUntil()) && $b->getSchema()->getType() == HRBuffSchema::RESOURCES_ALL_TYPE)
                $bonus += $b->getSchema()->getValue();
        }
        $bonus += $this->hero->getSchema()->getBonusCollectRate() + ($this->hero->getSchema()->getBonusCollectRatePerLevel() * $this->hero->getLevel());
        $bonus /= 100.0;
        // add the resources
        $rtypes = array('water', 'pureWater', 'fuel', 'steel');
        foreach ($rtypes as $type) {
            $r[$type.'Gain'] *= $bonus;
        }
        return $r;
    }

    /**
    *   \brief remove a certain amount of a given resource from the resources array
    * @param String $key Key of the resource('water','pureWater','steel','fuel')
    * @param integer $quantity Quantity to remove
    */
    public function removeResource($resourceKey, $quantity) {
        if (array_key_exists($resourceKey, $this->resources))
            $this->resources[$resourceKey] -= $quantity;
    }

    /**
    *   \brief Add a certain amount of a given resource from the resources array
    * @param String $key Key of the resource('water','pureWater','steel','fuel')
    * @param integer $quantity Quantity to add
    */
    public function addResource($resourceKey, $quantity) {
        if (array_key_exists($resourceKey, $this->resources))
            $this->resources[$resourceKey] = $quantity;
    }

    /**
    * \brief returns a resources array filled with the given quantity
    * @param integer $quantity Quantity for each resource
    */
    public static function createResourcesArray($quantity)
    {
        return array(
                     'water' => $quantity,
                     'waterStock' => 0,
                     'waterGain' => 0,
                     'pureWater' => $quantity,
                     'pureWaterStock' => 0,
                     'pureWaterGain' => 0,
                     'steel' => $quantity,
                     'steelStock' => 0,
                     'steelGain' => 0,
                     'fuel' => $quantity,
                     'fuelStock' => 0,
                     'fuelGain' => 0
                     );
    }

}
