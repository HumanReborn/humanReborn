<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HRForumTopicRepository
 *
 * \brief Repository for the HRForumTopic entity
 * 
 */
class HRForumTopicRepository extends EntityRepository
{
    /**
     * \brief return a HRForumTopic using its ID
     * @param integer $topicID
     * @return HRForumTopic
     */
    public function getTopic($topicID)
    {
        return $this->_em->createQuery("
                SELECT t,p,u
                FROM EIPHRBundle:HRForumTopic t
                LEFT JOIN t.posts p
                JOIN t.user u
                WHERE t.id = :topicid
                ORDER BY p.postedOn ASC
            ")
                ->setParameter(':topicid', $topicID)
                ->getSingleResult();
    }

    /**
     * \brief return all the topics in a section
     * @param integer $sectionid
     * @return HRForumTopic[]
     */
    public function getTopicsFromSection($sectionid)
    {
        return  $this->_em->createQuery("
                SELECT t
                FROM EIPHRBundle:HRForumTopic t
                WHERE t.section = :sectionid
                ORDER BY t.title ASC
            ")
                ->setParameter(':sectionid', $sectionid)
                ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }

    /**
     * \brief search the forum for a topic with its name containing $needle
     * @param string $needle
     * @return HRForumTopic[]
     */
    public function searchFor($needle)
    {
         return  $this->_em->createQuery("
                SELECT t
                FROM EIPHRBundle:HRForumTopic t
                WHERE t.title LIKE :needle
                ORDER BY t.title ASC
            ")
                ->setParameter(':needle', '%'.$needle.'%')
                ->getResult()
                 ;
    }
}
