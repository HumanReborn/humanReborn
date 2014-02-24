<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRQuestSchema
 *
 * @ORM\Table(name="quests_schemas")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRQuestSchemaRepository")
 */
class HRQuestSchema
{
    const GIVE_RESOURCES = 1;
    const DESTROY_UNIT = 2;
    const GO_TO = 3;
    const BUILD = 4;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\Column(name="name", type="string", length=128) */
    protected $name;

    /** @ORM\Column(name="description", type="string", length=64) */
    protected $description;

    /** @ORM\Column(name="type", type="integer") */
    protected $type;

    /** @ORM\Column(name="xpReward", type="integer") */
    protected $xpReward;

    /** @ORM\ManyToMany(targetEntity="EIP\HRBundle\Entity\HRItemSchema")
     *  @ORM\JoinTable(name="quest_item_rewards", joinColumns={ @ORM\JoinColumn(name="quest_schema_id", referencedColumnName="id")},
     *                                              inverseJoinColumns={ @ORM\JoinColumn(name="item_schema_id", referencedColumnName="id")}
     *                )
     */
    protected $itemReward;

    /** @ORM\Column(type="boolean") */
    protected $once;

    /** @ORM\Column(type="array") */
    protected $data;


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
     * Set name
     *
     * @param string $name
     * @return HRQuestSchema
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set description
     *
     * @param string $description
     * @return HRQuestSchema
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return HRQuestSchema
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set xpReward
     *
     * @param integer $xpReward
     * @return HRQuestSchema
     */
    public function setXpReward($xpReward)
    {
        $this->xpReward = $xpReward;

        return $this;
    }

    /**
     * Get xpReward
     *
     * @return integer
     */
    public function getXpReward()
    {
        return $this->xpReward;
    }

    public function getOnce() {
        return $this->once;
    }

    public function setOnce($once) {
        $this->once = $once;
    }

    public function getTypeName(){
        $types = array(
            1 => 'type.give.resources',
            2 => 'type.destroy.unit',
            3 => 'type.goto',
            4 => 'type.build'
        );
        return  $types[$this->type];
    }

    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getItemReward() {
        return $this->itemReward;
    }

    function __construct() {
        $this->itemReward = new \Doctrine\Common\Collections\ArrayCollection();
        $this->data = array();
        $this->once = false;
    }

}
