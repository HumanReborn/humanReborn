<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRItemSchema
 * \brief Item Schema
 *
 * \cond @ORM\Table(name="items_schemas") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRItemSchemaRepository") \endcond
 */
class HRItemSchema
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the schema */

    /** \cond @ORM\Column(name="name", type="string", length=80, nullable=false)  \endcond */
    protected $name;

    /** \cond @ORM\Column(name="image", type="string", length=40, nullable=false)  \endcond */
    protected $image;

    /** \cond @ORM\Column(name="description", type="string", length=128, nullable=false) \endcond */
    protected $description;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRBuffSchema")  \endcond */
    protected $buffSchema;

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRUnitSchema")  \endcond */
    protected $unitSchema;

    /** \cond @ORM\Column(name="resource_name", type="string", length=20, nullable=true) \endcond */
    protected $resourceName;

    /** \cond @ORM\Column(name="value", type="integer", nullable=true) \endcond */
    protected $value;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getImage() {
        return $this->image;
    }

    public function setImage($image) {
        $this->image = $image;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getBuffSchema() {
        return $this->buffSchema;
    }

    public function setBuffSchema($buffSchema) {
        $this->buffSchema = $buffSchema;
    }

    public function getUnitSchema() {
        return $this->unitSchema;
    }

    public function setUnitSchema($unitSchema) {
        $this->unitSchema = $unitSchema;
    }

    public function getResourceName() {
        return $this->resourceName;
    }

    public function setResourceName($resourceName) {
        $this->resourceName = $resourceName;
    }

    public function getValue() {
        return $this->value;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    function __construct($name = '', $image = '') {
    $this->name = $name;
    $this->image = $image;
    $this->description = '';
    }

}
