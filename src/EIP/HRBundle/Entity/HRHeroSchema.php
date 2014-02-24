<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EIP\HRBundle\Entity\HRHeroSchema
 *
 * @ORM\Table(name="heroes_schemas")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRHeroSchemaRepository")
 */
class HRHeroSchema
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int $img
     *
     * @ORM\Column(name="img", type="integer")
     */
    protected $img;


    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=40)
     */
    private $name;

    /**
     * @var float $bonusAttack
     *
     * @ORM\Column(name="bonusAttack", type="float")
     */
    protected $bonusAttack;

    /**
     * @var float $bonusArmor
     *
     * @ORM\Column(name="bonusArmor", type="float")
     */
    protected $bonusArmor;

    /**
     * @var float $bonusSpeed
     *
     * @ORM\Column(name="bonusSpeed", type="float")
     */
    protected $bonusSpeed;

    /**
     * @var float $bonusHealth
     *
     * @ORM\Column(name="bonusHealth", type="float")
     */
    protected $bonusHealth;

    /**
     * @var float $bonusCollectRate
     *
     * @ORM\Column(name="bonusCollectRate", type="float")
     */
    protected $bonusCollectRate;

    /** @ORM\Column(type="float")  */
    protected $bonusHealthPerLevel;
    /** @ORM\Column(type="float")  */
    protected $bonusAttackPerLevel;
    /** @ORM\Column(type="float")  */
    protected $bonusArmorPerLevel;
    /** @ORM\Column(type="float")  */
    protected $bonusSpeedPerLevel;
    /** @ORM\Column(type="float")  */
    protected $bonusCollectRatePerLevel;

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
     * Get img
     *
     * @return integer
     */
    public function getImg() {
        return $this->img;
    }
    public function getImage() {
        return $this->img;
    }

    /**
     * Set img
     *
     * @param integer $img
     */
    public function setImg($img) {
        $this->img = $img;
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
     * Set name
     *
     * @param string $name
     * @return HRHeroSchema
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->name.'.desc';
    }

     /**
     * Get bonusAttack
     *
     * @return float
     */
    public function getBonusAttack() {
        return $this->bonusAttack;
    }

    /**
     * Set bonusAttack
     *
     * @param float $bonusAttack
     */
    public function setBonusAttack($bonusAttack) {
        $this->bonusAttack = $bonusAttack;
    }

     /**
     * Get bonusArmor
     *
     * @return float
     */
    public function getBonusArmor() {
        return $this->bonusArmor;
    }

    /**
     * Set bonusArmor
     *
     * @param float $bonusArmor
     */
    public function setBonusArmor($bonusArmor) {
        $this->bonusArmor = $bonusArmor;
    }

     /**
     * Get bonusSpeed
     *
     * @return float
     */
    public function getBonusSpeed() {
        return $this->bonusSpeed;
    }

    /**
     * Set bonusSpeed
     *
     * @param float $bonusSpeed
     */
    public function setBonusSpeed($bonusSpeed) {
        $this->bonusSpeed = $bonusSpeed;
    }

     /**
     * Get bonusHealth
     *
     * @return float
     */
    public function getBonusHealth() {
        return $this->bonusHealth;
    }

    /**
     * Set bonusHealth
     *
     * @param float $bonusHealth
     */
    public function setBonusHealth($bonusHealth) {
        $this->bonusHealth = $bonusHealth;
    }

     /**
     * Get bonusCollectRate
     *
     * @return float
     */
    public function getBonusCollectRate() {
        return $this->bonusCollectRate;
    }

    /**
     * Set bonusCollectRate
     *
     * @param float $bonusCollectRate
     */
    public function setBonusCollectRate($bonusCollectRate) {
        $this->bonusCollectRate = $bonusCollectRate;
    }

    public function __construct(){
        $this->bonusSpeed = 0;
        $this->bonusAttack = 0;
        $this->bonusCollectRate = 0;
        $this->bonusHealth = 0;
        $this->bonusArmor = 0;
        $this->img = 0;

        $this->bonusArmorPerLevel = 0;
        $this->bonusAttackPerLevel = 0;
        $this->bonusSpeedPerLevel = 0;
        $this->bonusHealthPerLevel = 0;
        $this->bonusCollectRatePerLevel = 0;
    }

    public function getBonusHealthPerLevel() {
        return $this->bonusHealthPerLevel;
    }

    public function setBonusHealthPerLevel($bonusHealthPerLevel) {
        $this->bonusHealthPerLevel = $bonusHealthPerLevel;
    }

    public function getBonusAttackPerLevel() {
        return $this->bonusAttackPerLevel;
    }

    public function setBonusAttackPerLevel($bonusAttackPerLevel) {
        $this->bonusAttackPerLevel = $bonusAttackPerLevel;
    }

    public function getBonusArmorPerLevel() {
        return $this->bonusArmorPerLevel;
    }

    public function setBonusArmorPerLevel($bonusArmorPerLevel) {
        $this->bonusArmorPerLevel = $bonusArmorPerLevel;
    }

    public function getBonusSpeedPerLevel() {
        return $this->bonusSpeedPerLevel;
    }

    public function setBonusSpeedPerLevel($bonusSpeedPerLevel) {
        $this->bonusSpeedPerLevel = $bonusSpeedPerLevel;
    }

    public function getBonusCollectRatePerLevel() {
        return $this->bonusCollectRatePerLevel;
    }

    public function setBonusCollectRatePerLevel($bonusCollectRatePerLevel) {
        $this->bonusCollectRatePerLevel = $bonusCollectRatePerLevel;
    }

}
