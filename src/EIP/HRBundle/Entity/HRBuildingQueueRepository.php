<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRBuildingQueueRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRBuildingQueueRepository extends EntityRepository
{

    /**
     * Fetch townQueuedBuildings
     * \brief return a list of HRBuilding which matches with the HRTown $town
	 *
     * @param HRTown $town
	 *
     * @return HRBuilding[]
     */
    public function fetchTownQueuedBuildings(HRTown $town) {
        return $this->_em->createQuery("
                SELECT b, s
                FROM EIPHRBundle:HRBuildingQueue b
                JOIN b.schema s
                WHERE b.town = :townid
                ORDER BY b.id ASC
            ")
                ->setParameter(':townid', $town->getId())
                ->getResult();
    }

    /**
     * Fetch lastBuildingCompletionTime
     * \brief return a bigint which represents the last building completion time
	 *
     * @param int $townid
	 *
     * @return bigint
     */
    public function fetchLastBuildingCompletionTime($townid) {
        $ctime = time();
        try
        {
            $result = $this->_em->createQuery("
                SELECT b.endTime
                FROM EIPHRBundle:HRBuildingQueue b
                WHERE b.town = :townid
                AND b.endTime > :time
                ORDER BY b.endTime DESC
                ")
                    ->setParameter(':townid', $townid)
                    ->setParameter(':time', $ctime)
                    ->setMaxResults(1)
                    ->getSingleScalarResult();
        }
        catch (\Exception $ex)
        {
            $result = $ctime;
        }
        return $result;
    }

    /**
     * Fetch userQueue
     * \brief return a list of HRBuilding which matches with both of the $userid and the $gameid
	 *
     * @param int $userid
     * @param int $gameid
	 *
     * @return HRBuilding[]
     */
    public function fetchUserQueue($userid, $gameid)
    {
        return $this->_em->createQuery("
                SELECT b, s
                FROM EIPHRBundle:HRBuildingQueue b
                JOIN b.schema s
                WHERE b.user = :userid
                AND b.game = :gameid
                ORDER BY b.endTime ASC
            ")
                ->setParameter(':gameid', $gameid)
                ->setParameter(':userid', $userid)
                ->getResult();
    }

    public function removeQueuedBuildingsFromTown(HRTown $town) {
        $this->_em->createQuery("DELETE EIPHRBundle:HRBuildingQueue q WHERE q.town = :townid")
        ->setParameter(':townid', $town->getId())
        ->execute();
    }

}
