<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EIP\HRBundle\Entity\HRArmyMovement
 *
 * @ORM\Table(name="army_movements")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRArmyMovementRepository")
 */
class HRArmyMovement
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
     * @var integer $fromX
     *
     * @ORM\Column(name="fromX", type="integer")
     */
    private $fromX;

    /**
     * @var integer $fromY
     *
     * @ORM\Column(name="fromY", type="integer")
     */
    private $fromY;

    /**
     * @var integer $toX
     *
     * @ORM\Column(name="toX", type="integer")
     */
    private $toX;

    /**
     * @var integer $toY
     *
     * @ORM\Column(name="toY", type="integer")
     */
    private $toY;

    /**
     * @var integer $startTime
     *
     * @ORM\Column(name="startTime", type="bigint")
     */
    private $startTime;

    /**
     * @var integer $endTime
     *
     * @ORM\Column(name="endTime", type="bigint")
     */
    private $endTime;

    /**
     * @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRArmy", inversedBy="movement")
     * @Assert\NotNull()
     */
    protected $army;

     /**
     * @var boolean finished
     *
     * @ORM\Column(name="finished", type="boolean")
     */
    protected $finished;

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
     * Set fromX
     *
     * @param integer $fromX
     * @return HRArmyMovement
     */
    public function setFromX($fromX)
    {
        $this->fromX = $fromX;

        return $this;
    }

    /**
     * Get fromX
     *
     * @return integer
     */
    public function getFromX()
    {
        return $this->fromX;
    }

    /**
     * Set fromY
     *
     * @param integer $fromY
     * @return HRArmyMovement
     */
    public function setFromY($fromY)
    {
        $this->fromY = $fromY;

        return $this;
    }

    /**
     * Get fromY
     *
     * @return integer
     */
    public function getFromY()
    {
        return $this->fromY;
    }

    /**
     * Set toX
     *
     * @param integer $toX
     * @return HRArmyMovement
     */
    public function setToX($toX)
    {
        $this->toX = $toX;

        return $this;
    }

    /**
     * Get toX
     *
     * @return integer
     */
    public function getToX()
    {
        return $this->toX;
    }

    /**
     * Set toY
     *
     * @param integer $toY
     * @return HRArmyMovement
     */
    public function setToY($toY)
    {
        $this->toY = $toY;

        return $this;
    }

    /**
     * Get toY
     *
     * @return integer
     */
    public function getToY()
    {
        return $this->toY;
    }

    /**
     * Set startTime
     *
     * @param integer $startTime
     * @return HRArmyMovement
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return integer
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param integer $endTime
     * @return HRArmyMovement
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return integer
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    public function getArmy() {
        return $this->army;
    }

    public function setArmy(HRARmy $army) {
        $this->army = $army;
    }

    public function getFinished() {
        return $this->finished;
    }

    public function setFinished($finished) {
        $this->finished = $finished;
    }

    public function __construct($army, $fromX, $fromY) {
        $this->army = $army;
        $this->startTime = 0;
        $this->endTime = 0;
        $this->fromX = $fromX;
        $this->fromY = $fromY;
        $this->toX = $fromX;
        $this->toY = $fromY;
        $this->finished = true;
    }

    /**
    * \brief starts a movement to the given coordinates. Throws an exception if the army is already moving
    * Do not forget to update map.html.twig  javascript 'getMovementTime' function if the algorithm is changed here
    */
    public function update($toX, $toY, &$moving) {
        if (!$this->finished)
            throw new \Exception("Update on non completed movement.");
        if ($toX == $this->fromX && $toY == $this->fromY) return;
        $this->startTime = time();
        $duration = abs($toX - $this->fromX) + abs($toY - $this->fromY);
        if ($duration > 100)
        {
            if ($duration > 500)
                $duration *= 50;
            else if ($duration > 200)
                $duration *= 20;
            else
                $duration *= 5;
        }
        $this->endTime = $this->startTime + $duration;
        $this->toX = $toX;
        $this->toY = $toY;
        $this->finished = false;
        $moving = true;
    }

}
