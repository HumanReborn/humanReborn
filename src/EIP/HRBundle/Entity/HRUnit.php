<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRUnit
 * \brief Unit entity, contained in an army
 *
 * \cond @ORM\Table(name="units") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRUnitRepository") \endcond
 */
class HRUnit
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    protected $id; /**< id of the unit*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRArmy", inversedBy="units")  \endcond */
    protected $army; /**< HRArmy which the unit belongs to*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUnitSchema")  \endcond */
    protected $schema; /**< HRUnitSchema of the unit*/

    protected $tmpHp; /**< temporary health used in fights resolution, not stored in database */

    protected $alive; /**< alive flag used in fights resolution, not stored in database */

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
     * Get army
     *
     * @return HRArmy
     */
    public function getArmy() {
        return $this->army;
    }

    /**
     * Set Army
     *
     * @param HRArmy $army
     */
    public function setArmy($army) {
        $this->army = $army;
    }

    /**
     * Get schema
     *
     * @return HRUnitSchema
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param HRUnitSchema $schema
     */
    public function setSchema($schema) {
        $this->schema = $schema;
    }

    public function getTmpHp() {
        return $this->tmpHp;
    }

    public function setTmpHp($tmpHp) {
        $this->tmpHp = $tmpHp;
    }

    public function getAlive() {
        return $this->alive;
    }

    public function setAlive($alive) {
        $this->alive = $alive;
    }

    /**
     * \brief Check if the unit is flagged for removal in the next 'flush' operation
     * @param \Doctrine\ORM\EntityManager $em
     * @return boolean
     */
    public function isFlaggedForRemoval(\Doctrine\ORM\EntityManager $em){
        return ($em->getUnitOfWork()->getEntityState($this) == \Doctrine\ORM\UnitOfWork::STATE_REMOVED);
    }

    /**
     * \brief Update the health of the unit in a fight
     * @param integer $amount
     * @throws \Exception
     */
    public function receiveDamage($amount)
    {
        $this->tmpHp -= $amount;
        if ($this->tmpHp <= 0 )
            throw new \Exception("Unit with negative temporary health");
    }

    /**
     * \brief Set the hp of the unit to that of its schema plus the currently available health buffs
     * @param float $totalHealthBuff
     */
    public function initializeTemporaryHp($totalHealthBuff)
    {
        $this->tmpHp = $this->schema->getHp() + $totalHealthBuff;
    }

    public function __construct() {
        $this->alive = true;
    }

}
