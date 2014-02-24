<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRArmy
 * \brief Army entity
 *
 * \cond @ORM\Table(name="armies") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRArmyRepository") \endcond
 */
class HRArmy
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the Army*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRGame")  \endcond */
    protected $game; /**< HRGame which the Army belongs to*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUser")  \endcond */
    protected $user; /**< HRUser which the Army belongs to*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRTown", inversedBy="armies")  \endcond */
    protected $town; /**< HRTown which the Army belongs to*/

    /** \cond @ORM\Column(name="moving", type="boolean", nullable=false)  \endcond */
    protected $moving; /**< is true if the Army is moving*/

    /** \cond @ORM\OneToMany(targetEntity="EIP\HRBundle\Entity\HRUnit", mappedBy="army", cascade={"remove"})  \endcond */
    protected $units; /**< */

    /** \cond @ORM\Column(name="garrison", type="boolean", nullable=false)  \endcond */
    protected $garrison; /**< is true if the Army is a garrison*/

    /**
     * @ORM\OneToOne(targetEntity="EIP\HRBundle\Entity\HRArmyMovement", mappedBy="army", cascade={"persist", "remove"})
     */
    protected $movement;


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
     * Get game
     *
     * @return HRGame
     */
    public function getGame() {
        return $this->game;
    }

    /**
     * Set game
     *
     * @param HRGame $game
     */
    public function setGame(HRGame $game) {
        $this->game = $game;
    }

    /**
     * Get user
     *
     * @return HRUser
     */
    public function getUser() {
        return $this->user;
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
     * Get town
     *
     * @return HRTown
     */
    public function getTown() {
        return $this->town;
    }

    /**
     * Set town
     *
     * @param HRTown $town
     */
    public function setTown(HRTown $town) {
        $this->town = $town;
    }

    /**
     * Get moving
     *
     * @return boolean
     */
    public function getMoving() {
        return $this->moving;
    }

    /**
     * Set moving
     *
     * @param boolean $moving
     */
    public function setMoving($moving) {
        $this->moving = $moving;
    }

    /**
     * @param HRUser $user
     * @param HRGame $game
     * @param HRTown $town
     */
    public function __construct(HRUser $user = null, HRGame $game = null, HRTown $town = null) {
        $this->user = $user;
        $this->game = $game;
        $this->town = $town;
        $this->moving = false;
        $this->garrison = false;
        if ($town)
            $this->movement = new HRArmyMovement($this, $this->town->getXCoord(), $this->town->getYCoord());
        else
            $this->movement = new HRArmyMovement($this, 0,0);
        $this->units = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get units
     *
     * @return HRUnit
     */
    public function getUnits() {
        return $this->units;
    }

    /**
     * Set units
     *
     * @param HRUnit $units
     */
    public function setUnits($units) {
        $this->units = $units;
    }

    /**
     * Get garrison
     *
     * @return boolean
     */
    public function isGarrison() {
        return $this->garrison;
    }

    public function getGarrison()
    {
        return $this->garrison;
    }

    /**
     * Set garrison
     *
     * @param boolean $garrison
     */
    public function setGarrison($garrison) {
        $this->garrison = $garrison;
    }

    public function getMovement() {
        return $this->movement;
    }

    public function setMovement(HRArmyMovement $movement) {
        $this->movement = $movement;
    }

    /**
     * \brief updates the movement of the army, send it back to town
     */
    public function goBackToTown() {
        $this->movement->update($this->town->getXCoord(), $this->town->getYCoord(), $this->moving);
    }

    /**
     * \brief start a movement to the given coordinates
     * @param integer $toX
     * @param integer $toY
     */
    public function move($toX, $toY) {
        $this->movement->update($toX, $toY, $this->moving);
    }

    /**
     *
     * @param type $units
     * @return array([schemaID: { schemaName, number }])
     */
    public static function getUnitsCountArray($units) {
        $content = array();
        foreach ($units as $u)
        {
            if (!array_key_exists($u->getSchema()->getId(), $content))
                $content[$u->getSchema()->getId()] = array(
                    "name" => $u->getSchema ()->getName(),
                    "number" => 1,
                    "schema" => $u->getSchema()->getId(),
                    "image" => $u->getSchema()->getImg(),
                    );
            else
                $content[$u->getSchema()->getId()]["number"] += 1;
        }
        return $content;
    }

    /**
     * \brief returns a hash containing the schemaId => count for each unit of the army
     * @return array
     */
    public function getUnitsCount() {
        return self::getUnitsCountArray($this->units);
    }

    /**
     * \brief Transfer units from an army to another
     * @param \EIP\HRBundle\Entity\HRARmy $receivingArmy
     * @param integer $schemaID
     * @param integer $nbToTransfer
     */
    public function transferTroops(HRARmy $receivingArmy, $schemaID, $nbToTransfer) {
        $cpt = 0;
        foreach ($this->units as $u)
        {
            if ($cpt >= $nbToTransfer)
                return ;
            if ($u->getSchema()->getId() == $schemaID)
            {
                $this->units->removeElement($u);
                $receivingArmy->getUnits()->add($u);
                $u->setArmy($receivingArmy);
                $cpt++;
            }
        }
    }

    /**
     * \brief Start a movement toward the given town
     * @param \EIP\HRBundle\Entity\HRTown $town
     */
    public function attackTown(HRTown $town)
    {
        $toX = $town->getXCoord();
        $toY = $town->getYCoord();
        $this->movement->update($toX, $toY, $this->moving);
    }

    /**
     * \brief returns the xp won by defeating the army
     * @return integer
     */
    public function getXpValue()
    {
        $total = 0;
        foreach ($this->units as $unit)
        {
            $total += $unit->getSchema()->getHp();
        }
        return $total;
    }

    /**
     * \brief return true if all the units in the army are dead
     * @param \Doctrine\ORM\EntityManager $em
     * @return boolean
     */
    public function isDefeated()
    {
        if ($this->units->count() == 0)
            return true;
        foreach ($this->units as $unit)
        {
            if ($unit->getAlive() === true)
                return false;
        }
        return true;
    }

    /**
     * \brief calculate and return the fighting score of the army
     * @param HRBuff[] $buffs
     * @param \Doctrine\ORM\EntityManager $em
     * @return integer
     */
    public function getFightingScore($buffs)
    {
        $score = 0.0;
        foreach ($this->units as $unit)
        {
            if ($unit->getAlive() === false) continue;
            $score += $unit->getSchema()->getAttack() + $buffs['attack'];
        }
        return $score;
    }

    /**
     * \brief absorb damages, once the hp of a unit reach 0, it is flagged for removal, units are supposed to be ordered by type here
     * @param integer $damage
     */
    public function receiveDamage($damage, $buffs)
    {
        foreach ($this->units as &$u)
        {
            if ($u->getAlive() === false) continue;
            $buffedArmor = ($u->getSchema()->getArmor() + $buffs['armor']);
            $damage -= $buffedArmor;
            // minimum received damage set to 1 to avoir unlimited fights
            if ($damage <= 1.0)
            {
                $damage = 1.0;
            }

            if ($damage >= $u->getTmpHp())
            {
                $damage -= $u->getTmpHp();
                $u->setAlive(false);
            }
            else
            {
                $u->receiveDamage($damage);
                $damage = 0;
                return ;
            }
        }
    }

    /**
     * \brief apply health buff to units and set their tmpHp attributes
     * @param float $hpBuff
     */
    public function initializeUnitsTmpHp($hpBuff)
    {
        foreach ($this->units as &$u) {
            $u->initializeTemporaryHp($hpBuff);
            $u->setAlive(true);
        }
    }

    /**
     * \brief return an array of lost units
     * @param \Doctrine\ORM\EntityManager $em
     * @return array
     */
    public function getLostUnits(\Doctrine\ORM\EntityManager $em)
    {
        $lost = array();
        foreach ($this->units as $u)
        {
            if ($u->getAlive() === false)
            {
                $em->remove($u);
                $schemaID = $u->getSchema()->getId();
                if (array_key_exists($schemaID, $lost))
                    $lost[$schemaID] += 1;
                else
                    $lost[$schemaID] = 1;
                $this->units->removeElement($u);
            }
        }
        return $lost;
    }

}
