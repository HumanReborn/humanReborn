<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRUnitSchemaRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRUnitSchemaRepository extends EntityRepository
{
    /**
     * Get getDescription
     * \brief return a schema and its description
     * @param integer $id
     * @return HRUnitSchema
     */
    public function getDescription($id) {
        return $this->_em
                        ->createQuery("SELECT s,d FROM EIPHRBundle:HRUnitSchema s JOIN s.description d WHERE s.id = :id")
                        ->setParameter(':id', $id)
                        ->getSingleResult();
    }

    public function getAllTranslatedNameAndImage($translator) {
        $result = $this->_em->createQuery("
                SELECT us.id id, us.name name, us.img image
                FROM EIPHRBundle:HRUnitSchema us
            ")
                ->getArrayResult();
        foreach ($result as &$r) {
            $r['name'] = $translator->trans($r['name'], array(), 'units');
        }
        return $result;
    }

    public function getSchemaName($schemaid) {
        return $this->_em->createQuery("SELECT s.name FROM EIPHRBundle:HRUnitSchema s WHERE s.id = :schemaid")
        ->setParameter(':schemaid', $schemaid)
        ->getSingleScalarResult();
    }
}
