<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRQuest
 *
 * @ORM\Table(name="quests")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRQuestRepository")
 */
class HRQuest
{
    const STATE_ONGOING = 1;
    const STATE_VICTORY = 2;
    const STATE_FINISHED = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRQuestSchema")    \endcond */
    protected $schema;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")    \endcond */
    protected $user;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")    \endcond */
    protected $game;

    /**
     * @var boolean
     *
     * @ORM\Column(name="state", type="smallint")
     */
    protected $state;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="beginTime", type="datetime")
     */
    protected $beginTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="datetime", nullable=true)
     */
    protected $endTime;

    /** @ORM\Column(type="array") */
    protected $data; /**< data, hash containing informations about the current state of the quest */

     /**  @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGameLink", inversedBy="quests")  */
    protected $gamelink;

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
     * Set beginTime
     *
     * @param \DateTime $beginTime
     * @return HRQuest
     */
    public function setBeginTime($beginTime)
    {
        $this->beginTime = $beginTime;

        return $this;
    }

    /**
     * Get beginTime
     *
     * @return \DateTime
     */
    public function getBeginTime()
    {
        return $this->beginTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return HRQuest
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function setSchema(HRQuestSchema $schema) {
        $this->schema = $schema;
        $r = array();
        foreach ($schema->getData() as $k => $v)
        {
            $r[$k] = 0;
        }
        $this->data = $r;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getGame() {
        return $this->game;
    }

    public function setGame($game) {
        $this->game = $game;
    }

    public function getState() {
        return $this->state;
    }

    public function setState($state) {
        $this->state = $state;
    }

    public function getGameLink() {
        return $this->gamelink;
    }

    public function setGameLink(HRGameLink $gamelink) {
        $this->gamelink = $gamelink;
    }

    public function __construct() {
        $this->beginTime = new \DateTime("now");
        $this->state = self::STATE_ONGOING;
        $this->data = array();
    }

    public function finish() {
        $this->endTime = new \DateTime("now");
        $this->state = self::STATE_FINISHED;
    }

    public function getData() {
        return $this->data;
    }

    public function setData(array $data) {
        $this->data = $data;
    }

    /**
     * \brief check if a quest is completed by comparing the data attribute to the data attribute of the schema
     * @return boolean
     */
    public function isCompleted() {
        foreach ($this->getSchema()->getData() as $k => $v)
        {
            if ($this->data[$k] < $v)
                return false;
        }
        $this->endTime = new \DateTime('now');
        $this->state = self::STATE_VICTORY;
        return true;
    }

    public function getStateName() {
        if ($this->state == self::STATE_FINISHED)
            return 'quest.finished';
        if ($this->state == self::STATE_VICTORY)
            return 'quest.victory';
        return 'quest.ongoing';
    }

}
