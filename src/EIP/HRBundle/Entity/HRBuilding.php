<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * \class HRBuilding
 * \brief Building entity
 *
 * \cond @ORM\Table(name="buildings") \endcond
 * \cond @ORM\Entity(repositoryClass="EIP\HRBundle\Entity\HRBuildingRepository") \endcond
 */
class HRBuilding
{
    /**
     * \cond @ORM\Column(name="id", type="integer") \endcond
     * \cond @ORM\Id \endcond
     * \cond @ORM\GeneratedValue(strategy="AUTO") \endcond
     */
    private $id; /**< id of the Building*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRTown", inversedBy="buildings")  \endcond */
    protected $town; /**< HRTown which the Building belongs to*/

    /** \cond @ORM\ManyToOne(targetEntity="EIP\HRBundle\Entity\HRBuildingSchema")  \endcond */
    protected $schema; /**< HRBuildingSchema of the Building*/

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
     * Get schema
     *
     * @return HRBuildingSchema
     */
    public function getSchema() {
        return $this->schema;
    }

    /**
     * Set schema
     *
     * @param HRBuildingSchema $buildingSchema
     */
    public function setSchema(HRBuildingSchema $buildingSchema) {
        $this->schema = $buildingSchema;
    }

}
