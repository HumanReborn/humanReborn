<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * EIP\HRBundle\Entity\BattleReport
 * \class HRBattleReport
 * \brief BattleReport class
 * @ORM\Table(name="battle_reports")
 * @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRBattleReportRepository")
 */
class HRBattleReport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")
     *  @Assert\NotNull()
     */
    protected $attacker; /**< id of the attacker (HRUser) */

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")
     *  @Assert\NotNull()
     */
    protected $defender; /**< id of the defender (HRUser) */

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")
     *  @Assert\NotNull()
     */
    protected $winner; /**< id of the winner (HRUser) */

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame") */
    protected $game;

    /** @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRTown") */
    protected $town;  /**< id of the town attacked (HRTown) */

    //protected $quest; /**< id of the quest (HRQuest) */

    /** @ORM\Column(name="xp_won", type="integer") */
    protected $xpWon; /**< experience won */

    /** @ORM\Column(name="report_time", type="datetime") */
    protected $time; /**< time of creation of the battle report */

    /** @ORM\Column(name="lostUnits", type="array") */
    protected $lostUnits; /**< units lost in the battle */

    /** @ORM\Column(name="armies", type="array") */
    protected $armies; /**< armies involved in the fight before any casualty */

    public function getAttacker() {
        return $this->attacker;
    }

    public function setAttacker(HRUser $attacker) {
        $this->attacker = $attacker;
    }

    public function getDefender() {
        return $this->defender;
    }

    public function setDefender(HRUser $defender) {
        $this->defender = $defender;
    }

    public function getTown() {
        return $this->town;
    }

    public function setTown(HRTown $town) {
        $this->town = $town;
    }

    public function getItemsWon() {
        return $this->itemsWon;
    }

    public function setItemsWon($itemsWon) {
        $this->itemsWon = $itemsWon;
    }

    public function getXpWon() {
        return $this->xpWon;
    }

    public function setXpWon($xpWon) {
        $this->xpWon = $xpWon;
    }

    public function getTime() {
        return $this->time;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function getArmies() {
        return $this->armies;
    }

    public function setArmies(array $armies) {
        $this->armies = $armies;
    }

    public function getPlace() {
        if ($this->town)
            return $this->town;
        else if ($this->quest)
            return $this->quest->getSchema();
        throw new \Exception("Battle report with no place set");
    }

    public function getLoser() {
        return $this->winner->getId() !== $this->attacker->getId() ? $this->attacker : $this->defender;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
        $this->time = new \DateTime('now');
    }

    public function getWinner() {
        return $this->winner;
    }

    public function setWinner($winner) {
        $this->winner = $winner;
    }

    public function getGame() {
        return $this->game;
    }

    public function setGame($game) {
        $this->game = $game;
    }

    public function getLostUnits() {
        return $this->lostUnits;
    }

    public function setLostUnits($lostUnits) {
        $this->lostUnits = $lostUnits;
    }

    // static
    /**
     * \brief creates a new battlereport with the given information
     * @param \EIP\HRBundle\Entity\HRArmy $attacker
     * @param \EIP\HRBundle\Entity\HRArmy $defender
     * @param \EIP\HRBundle\Entity\HRArmy $winner
     * @param DateTime $nowDateTime
     * @param integer $xpWon
     * @param array $lostUnits
     * @return \EIP\HRBundle\Entity\HRBattleReport
     */
    public static function createBattleReport(HRArmy $attacker, HRArmy $defender, HRArmy $winner, $nowDateTime, $xpWon, $lostUnits, array $armies){
        $battleReport = new HRBattleReport();
        $battleReport->setAttacker($attacker->getUser());
        $battleReport->setDefender($defender->getUser());
        $battleReport->setWinner($winner->getUser());
        $battleReport->setTime($nowDateTime);
        $battleReport->setXpWon($xpWon);
        $battleReport->setTown($defender->getTown());
        $battleReport->setGame($winner->getGame());
        $battleReport->setLostUnits($lostUnits);
        $battleReport->setArmies($armies);
        return $battleReport;
    }

}
