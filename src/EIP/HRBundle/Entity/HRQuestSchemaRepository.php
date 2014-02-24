<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \brief entity repository for the HRQuestSchema entity
 */
class HRQuestSchemaRepository extends EntityRepository
{
    public function getAllSchemas() {
        return $this->_em->createQuery("
                SELECT qs,i FROM EIPHRBundle:HRQuestSchema qs
                LEFT JOIN qs.itemReward i
                ORDER BY qs.id
            ")
                ->getResult();
    }
    
}
