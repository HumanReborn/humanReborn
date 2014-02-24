<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRTechnologyQueueRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRTechnologyQueueRepository extends EntityRepository
{
    /**
     * Fetch lastUnitCompletionTime
     * \brief return an integer which represents the last technology completion time
	 *
     * @param int $townid
	 *
     * @return integer
     */
    public function fetchLastTechnologyCompletionTime($gameid, $userid) {
        try
        {
            $result = $this->_em->createQuery("
                SELECT u.endTime
                FROM EIPHRBundle:HRTechnologyQueue u
                WHERE u.game = :gameid
				AND u.user = :userid
                ORDER BY u.endTime DESC
                ")
                    ->setParameter(':gameid', $gameid)
                    ->setParameter(':userid', $userid)
                    ->setMaxResults(1)
                    ->getSingleScalarResult();
        }
        catch (\Exception $ex)
        {
            $result = time();
        }
        return $result;
    }

    /**
     * Fetch userQueue
     * \brief return a list of HRTechnology which matches with both of the $userid and the $gameid
	 *
     * @param int $userid
     * @param int $gameid
	 *
     * @return HRTechnology[]
     */
    public function fetchUserQueue($userid, $gameid)
    {
        return $this->_em->createQuery("
                SELECT t, s
                FROM EIPHRBundle:HRTechnologyQueue t
                JOIN t.schema s
                WHERE t.user = :userid
                AND t.game = :gameid
                ORDER BY t.endTime ASC
            ")
                ->setParameter(':gameid', $gameid)
                ->setParameter(':userid', $userid)
                ->getResult();
    }	
	
}
