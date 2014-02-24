<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HRForumSectionRepository
 *
 * \brief Repository for the HRForumSection entity
 * 
 */
class HRForumSectionRepository extends EntityRepository
{
    /**
     * 
     * \brief get a HRForumSection using its name
     * @param string $sectionName
     * @return HRForumSection
     */
    public function getSectionByName($sectionName)
    {
        return $this->_em->createQuery("
                SELECT s,t
                FROM EIPHRBundle:HRForumSection s
                LEFT JOIN s.topics t
                WHERE s.name = :sectionName
                ORDER BY t.sticky DESC, t.lastPostOn DESC
            ")
                ->setParameter(':sectionName', $sectionName)
                ->getSingleResult();
    }


}
