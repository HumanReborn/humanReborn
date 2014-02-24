<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRArmyRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRArmyRepository extends EntityRepository
{
    /**
     * Fetch allArmies
     * \brief return a list of HRArmy which contains all the armies
	 *
     * @return HRArmy[]
     */
    public function fetchAllArmies() {
        return $this->_em->createQuery("
            SELECT a
            FROM EIPHRBundle:HRArmy a
            ORDER BY a.id ASC
            ")->getResult();
    }

    /**
     * Fetch playerArmies
     * \brief return a list of HRArmy which matches with both of the $userid and the $gameid
	 *
     * @param int $userid
     * @param int $gameid
	 *
     * @return HRArmy[]
     */
    public function fetchPlayerArmies($userid, $gameid)
    {
        return $this->_em->createQuery("
            SELECT a, t
            FROM EIPHRBundle:HRArmy a
            JOIN a.town t
            WHERE a.user = :userid
            AND a.game = :gameid
            AND a.garrison = false
            ORDER BY t.id ASC
            ")
                ->setParameter(':userid', $userid)
                ->setParameter(':gameid', $gameid)
                ->getResult();
    }

    /**
     * Fetch playerGarrisons
     * \brief return a list of HRArmy  which are garrisons and matches with both of the $userid and the $gameid
	 *
     * @param int $userid
     * @param int $gameid
	 *
     * @return HRArmy[]
     */
    public function fetchPlayerGarrisons($userid, $gameid)
    {
        return $this->_em->createQuery("
            SELECT a, t
            FROM EIPHRBundle:HRArmy a
            JOIN a.town t
            WHERE a.user = :userid
            AND a.game = :gameid
            AND a.garrison = true
            ORDER BY t.id ASC
            ")
                ->setParameter(':userid', $userid)
                ->setParameter(':gameid', $gameid)
                ->getResult();
    }

    /**
     * Fetch getArmiesAt
     * \brief return a list of non-moving HRArmy  gathered at the specified location
	 *
     * @param HRGame $game
     * @param int $x
     * @param int $y
	 *
     * @return HRArmy[]
     */
    public function getArmiesAt(HRGame $game, $x, $y)
    {
        return $this->_em->createQuery("
            SELECT a, u, s
            FROM EIPHRBundle:HRArmy a
            LEFT JOIN a.units u
            LEFT JOIN u.schema s
            JOIN a.town t
            WHERE a.game = :gameid
            AND t.xCoord = :x
            AND t.yCoord = :y
            AND a.moving = false
            ORDER BY a.garrison DESC
        ")
                ->setParameter(':gameid', $game->getId())
                ->setParameter(':x', $x)
                ->setParameter(':y', $y)
                ->getResult();
    }

    /**
     * Fetch getTowwnGarrison
     * \brief return the garrison for a town
	 *
     * @param int $townid
	 *
     * @return HRArmy
     */
    public function getTownGarrison($townid) {
        // Fail toujours, malgre le fait qu'il existe une garnison pour la ville...
        return $this->_em->createQuery("
            SELECT a, u
            FROM EIPHRBundle:HRArmy a
            LEFT JOIN a.units u
            JOIN a.town t
            WHERE t.id = :townid
            AND a.garrison = true
        ")
                ->setParameter(':townid', $townid)
                ->getSingleResult();
    }

    /**
     * \brief get a list of the mergeable army for a certain army
     * @param \EIP\HRBundle\Entity\HRArmy $army
     * @return HRArmy[]
     */
    public function getMergeableArmies(HRArmy $army)
    {
        return $this->_em->createQuery("
                SELECT a, t, u
                FROM EIPHRBundle:HRArmy a
                JOIN a.town t
                LEFT JOIN a.units u
                WHERE a.town = :town
                AND a.moving  = false
                AND a != :army
            ")
                ->setParameter(':town', $army->getTown())
                ->setParameter(':army', $army)
                ->getResult();
    }

    /**
     * \brief get all the information about a HRArmy without the movement
     * @param integer $id
     * @return HRArmy
     */
    public function getArmyFullInfos($id)
    {
        return $this->_em->createQuery("
                SELECT a,t,u,s
                FROM EIPHRBundle:HRArmy a
                LEFT JOIN a.units u
                JOIN a.town t
                JOIN u.schema s
                WHERE a.id = :id
                AND a.moving = false
            ")
                ->setParameter(':id', $id)
                ->getSingleResult();
    }

    /**
     * \brief get all the information about a HRArmy
     * @param integer $id
     * @return integer
     */
    public function getArmyFullInfosAndMovement($id)
    {
        return $this->_em->createQuery("
                SELECT a,t,u,s,m
                FROM EIPHRBundle:HRArmy a
                LEFT JOIN a.units u
                JOIN a.town t
                JOIN u.schema s
                JOIN a.movement m
                WHERE a.id = :id
            ")
                ->setParameter(':id', $id)
                ->getSingleResult();
    }

    public function reAssignHostTownFor(HRTown $town, HRUser $user, HRTown $nearestTown) {
        $this->_em->createQuery("
                                    UPDATE EIPHRBundle:HRArmy a
                                    SET a.town = :nearestTownId
                                    WHERE a.town = :townid
                                    AND a.user = :userid
                                    AND a.garrison = false
                                ")
        ->setParameters(array(
                        ':userid' => $user->getId(),
                        ':townid' => $town->getId(),
                        ':nearestTownId' => $nearestTown->getId()
                        ))
        ->execute();
    }
}
