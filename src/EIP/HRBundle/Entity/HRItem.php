<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRItem
 * \brief Item entity
 *
 * \cond @ORM\Table(name="items") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRItemRepository") \endcond
 */
class HRItem
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the schema */

     /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRItemSchema")  \endcond */
    protected $schema; /**< schema of the item */

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRHero", inversedBy="items")  \endcond */
    protected $hero; /**< hero owning the item */

    public function getId() {
        return $this->id;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function setSchema(HRItemSchema $schema) {
        $this->schema = $schema;
    }

    public function getHero() {
        return $this->hero;
    }

    public function setHero(HRHero $hero) {
        $this->hero = $hero;
    }

    public function givesBuff() {
        return ($this->schema->getBuffSchema() != null);
    }

    public function givesResources() {
        return ($this->schema->getResourceName() != null);
    }

    public function givesUnits() {
        return ($this->schema->getUnitSchema() != null);
    }

}
