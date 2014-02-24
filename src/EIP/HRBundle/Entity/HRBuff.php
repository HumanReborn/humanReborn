<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRBuff
 *
 * @ORM\Table(name="buffs")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRBuffRepository")
 */
class HRBuff
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRBuffSchema")  \endcond */
    protected $schema;

    /** @ORM\Column(name="valid_until", type="bigint")    */
    protected $validUntil;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGameLink", inversedBy="buffs")  \endcond */
    protected $gameLink;

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

    public function setSchema(HRBuffSchema $schema) {
        $this->schema = $schema;
    }

    public function getValidUntil() {
        return $this->validUntil;
    }

    public function setValidUntil($validUntil) {
        $this->validUntil = $validUntil;
    }

    public function getGameLink() {
        return $this->gameLink;
    }

    public function setGameLink($gameLink) {
        $this->gameLink = $gameLink;
    }

    public function __construct(HRBuffSchema $schema, HRGameLink $gl) {
        $this->schema = $schema;
        $this->validUntil = time() + $schema->getDuration();
        $this->gameLink = $gl;
        $gl->getBuffs()->add($this);
    }

    /**
     * \brief sorts the buff array
     * @param array $buffs
     * @return array
     */
    public static function sortByType(array $buffs){
        $sorted = array(
            'health' => array(),
            'attack' => array(),
            'armor' => array(),
            'speed' => array(),
            'resources' => array(),
            'buildingTime' => array(),
            'trainingTime' => array()
        );
        foreach ($buffs as $buff) {
            switch ($buff->getSchema()->getType()) {
                case HRBuffSchema::ARMOR_ALL_TYPE: $key = 'armor'; break;
                case HRBuffSchema::ATTACK_ALL_TYPE: $key = 'attack'; break;
                case HRBuffSchema::HEALTH_ALL_TYPE: $key = 'health'; break;
                case HRBuffSchema::SPEED_ALL_TYPE: $key = 'speed'; break;
                case HRBuffSchema::RESOURCES_ALL_TYPE: $key = 'resources'; break;
                case HRBuffSchema::BUILDING_TIME_TYPE: $key = 'buildingTime'; break;
                case HRBuffSchema::TRAINING_TIME_TYPE: $key = 'trainingTime'; break;
                default: continue;
            }
            $sorted[$key][] = $buff;
        }
        return $sorted;
    }

    /**
     * \brief returns true if the buff is active at the given time
     * @param bigint $currenTime
     * @return boolean
     */
    public function isValid($currenTime)
    {
        return ($this->validUntil >= $currenTime || $this->schema->getPermanent());
    }

}
