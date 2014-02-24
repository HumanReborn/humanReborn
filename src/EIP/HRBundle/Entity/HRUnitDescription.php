<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HRUnitDescription
 *
 * \cond @ORM\Table(name="unit_descriptions") \endcond
 * \cond @ORM\Entity \endcond
 */
class HRUnitDescription
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** \cond @ORM\ OneToOne(targetEntity="EIP\HRBundle\Entity\HRUnitSchema", inversedBy="description")  \endcond */
    protected $schema;

    /** @ORM\Column(name="content", type="string", length=64, nullable=false) */
    protected $content;


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

    public function setSchema($schema) {
        $this->schema = $schema;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }


    function __construct(HRUnitSchema $schema) {
        $this->schema = $schema;
    }


}
