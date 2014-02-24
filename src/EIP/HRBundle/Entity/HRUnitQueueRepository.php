<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRUnitQueueRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRUnitQueueRepository extends EntityRepository
{
    /**
     * Fetch lastUnitCompletionTime
     * \brief return an integer which represents the last unit completion time
	 *
     * @param int $townid
	 *
     * @return integer
     */
    public function fetchLastUnitCompletionTime($townid) {
        try
        {
            $result = $this->_em->createQuery("
                SELECT u.endTime
                FROM EIPHRBundle:HRUnitQueue u
                JOIN u.army a
                WHERE a.town = :townid
                AND a.garrison = true
                ORDER BY u.endTime DESC
                ")
                    ->setParameter(':townid', $townid)
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
     * \brief return a list of HRUnit which matches with both of the $userid and the $gameid
	 *
     * @param int $userid
     * @param int $gameid
	 *
     * @return HRUnit[]
     */
    public function fetchUserQueue($userid, $gameid)
    {
        return $this->_em->createQuery("
                SELECT u, s
                FROM EIPHRBundle:HRUnitQueue u
                JOIN u.schema s
                JOIN u.army a
                WHERE a.user = :userid
                AND a.game = :gameid
                ORDER BY u.endTime ASC
            ")
                ->setParameter(':gameid', $gameid)
                ->setParameter(':userid', $userid)
                ->getResult();
    }


    /**
    * \brief returns the number of queue units for a player/game
    * @param interger $userId
    * @param interger $gameId
    * @return integer
    */
    public function getQueuedUnitsCount($userid, $gameid) {
        return $this->_em->createQuery('SELECT COUNT(q.id)
                                FROM EIPHRBundle:HRUnitQueue q 
                                JOIN q.army a WITH q.army = a.id
                                WHERE a.game = :gameid
                                AND a.user = :userid')
        ->setParameters(array(
                        ':gameid' => $gameid,
                        ':userid' => $userid
                        ))
        ->getSingleScalarResult();
    }

}
