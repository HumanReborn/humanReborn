<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \brief Repository for the HRItem entity
 * 
 */
class HRItemRepository extends EntityRepository
{
    /**
     * \brief return the information for a certain item based on the owning hero and the id of the item
     * @param integer $itemid
     * @param integer $hero
     * @return HRItem
     */
    public function getItemInfos($itemid, $hero) {
        return $this->_em->createQuery("
                SELECT i,s,u,b
                FROM EIPHRBundle:HRItem i
                JOIN i.schema s
                LEFT JOIN s.unitSchema u
                LEFT JOIN s.buffSchema b
                WHERE i.id = :itemid
                AND i.hero = :hero
            ")
                ->setParameters(array(
                    ':itemid' => $itemid,
                    ':hero' => $hero
                ))
                ->getSingleResult();
    }
}
