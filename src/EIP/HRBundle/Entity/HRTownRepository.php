<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRTownRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRTownRepository extends EntityRepository
{
    /**
     * Fetch towns
     * \brief return a list of all HRTown
	 *
     * @return HRTown[]
     */
    public function fetchTowns() {
        return  $this->_em->createQuery('
            SELECT  t town, o.username owner_username, g.name game_name
            FROM EIPHRBundle:HRTown t
            JOIN t.owner o
            JOIN t.game g
            ORDER BY t.id ASC
            ')->getArrayResult();
    }

    /**
     * \brief get the town located at the given coordinates in a game
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param integer $x
     * @param integer $y
     * @return HRTown
     */
    public function getTownAt(HRGame $game, $x, $y) {
        return  $this->_em->createQuery('
            SELECT t, u
            FROM EIPHRBundle:HRTown t
            JOIN t.owner u
            WHERE t.game = :gameid
            AND t.xCoord = :x
            AND t.yCoord = :y
            ')
                ->setParameter(':gameid', $game->getId())
                ->setParameter(':x', $x)
                ->setParameter(':y', $y)
                ->getSingleResult();
    }

    /**
     * \brief get a list of the HRTown that do NOT belong to the user in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRtowns[]
     */
    public function getTargetTowns(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery('
                SELECT t
                FROM EIPHRBundle:HRTown t
                WHERE t.owner != :id
                AND t.game = :gameid
                ORDER BY t.name ASC
            ')
                ->setParameter(':id', $user->getId())
                ->setParameter(':gameid', $game->getId())
                ->getResult();
    }

    /**
     * \brief get a list of the HRTown that DO belong to the user in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRtowns[]
     */
    public function getAllyTowns(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery("
                SELECT t
                FROM EIPHRBundle:HRTown t
                WHERE t.game = :game
                AND t.owner = :user
                ORDER BY t.name ASC
            ")
                ->setParameter(':game', $game->getId())
                ->setParameter(':user', $user->getId())
                ->getResult();
    }

    /**
     * \brief returns true if the town belongs to the user, false otherwise
     * @param integer $townid
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @return boolean
     */
    public function belongsTo($townid, HRUser $user) {
        $res = $this->_em->createQuery("
                SELECT t.id
                FROM EIPHRBundle:HRTown t
                WHERE t.id = :townid
                AND t.owner = :user
            ")
                ->setParameters(array(
                    ':townid' => $townid,
                    ':user' => $user->getId()
                ))
                ->getSingleScalarResult();
        return ($res > 0);
    }

    /**
     * \brief return all the information about a town in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRTown[]
     */
    public function getFullInformation(HRUser $user, HRGame $game) {
        return $this->_em->createQuery("
                SELECT t,a,u,s,m,d
                FROM EIPHRBundle:HRTown t
                LEFT JOIN t.armies a
                LEFT JOIN a.movement m
                LEFT JOIN a.units u
                LEFT JOIN u.schema s
                LEFT JOIN s.description d
                WHERE t.game = :game
                AND t.owner = :user
                ORDER BY t.name, s.id ASC
            ")
                ->setParameters(array(
                    ':user' => $user->getId(),
                    ':game' => $game->getId()
                ))
                ->getResult();
    }

    /**
     * \brief returns the towns of a player in a game and the HRBuildings linked to it
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRTown[]
     */
    public function getTownsList(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery("
                SELECT t,b,bq
                FROM EIPHRBundle:HRTown t
                LEFT JOIN t.buildings b
                LEFT JOIN t.buildingsQueue bq
                WHERE t.owner = :userid
                AND t.game = :gameid
                ORDER BY t.name
            ")
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                ))
                ->getResult();
    }

    /**
     * \brief get the town for a player in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRTown[]
     */
    public function getPlayerTowns(HRUser $user, HRGame $game) {
        return $this->_em->createQuery(
                "SELECT t,b,bq
                    FROM EIPHRBundle:HRTown t
                    LEFT JOIN t.buildings b
                    LEFT JOIN t.buildingsQueue bq
                    WHERE t.owner = :userid
                    AND t.game = :gameid
                    ORDER BY t.id ASC"
            )
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                ))
                ->getResult();
    }

    /**
     * \brief Get the towns and their associated armies/units/schemas in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRTown[]
     */
    public function getTownsAndArmiesForGame($userid, $gameid){
        $currentTime = time();
        $towns = $this->_em->createQuery("
                SELECT t, a, u, us, owner, m
                FROM EIPHRBundle:HRTown t
                LEFT JOIN t.armies a
                LEFT JOIN a.units u
                LEFT JOIN u.schema us
                LEFT JOIN a.movement m
                JOIN t.owner owner
                WHERE t.game = :gameid
            ")
                ->setParameters(array(
                    ':gameid' => $gameid,
                ))
                ->getResult();

        $resultArray = array();
        foreach ($towns as $t) {
            $currentTown = array(
                'townid' => $t->getId(),
                'townname' => $t->getName(),
                'owned' => $t->getOwner()->getId() == $userid,
                'ownerid' => $t->getOwner()->getId(),
                'ownername' => $t->getOwner()->getUsername(),
                'x' => $t->getXCoord(),
                'y' => $t->getYCoord(),
                'garrison' => null,
                'armies' => array(),
            );
            foreach ($t->getArmies() as $army) {
                // if town is not owned, dont send the number of units present in the town
                if (!$currentTown['owned'])
                {
                    $garr = array();
                    foreach ($army->getUnits() as $unit) {
                        if (array_key_exists($unit->getSchema()->getId(), $garr))
                            continue;
                        else
                            $garr[$unit->getSchema()->getId()] = '?';
                    }
                    $currentTown['garrison'] = $garr;
                    continue;
                }

                $armyUnits = array();
                foreach ($army->getUnits() as $unit) {
                    if (array_key_exists($unit->getSchema()->getId(), $armyUnits))
                        $armyUnits[$unit->getSchema()->getId()] += 1;
                    else
                        $armyUnits[$unit->getSchema()->getId()] = 1;
                }

                $destination = null;
                $timeToArrival = null;
                if ($army->getMoving()) {
                    foreach ($towns as $tmpTown) {
                        if ($army->getMovement()->getToX() == $tmpTown->getXCoord()
                                && $army->getMovement()->getToY() == $tmpTown->getYCoord())
                        {
                            $destination = $tmpTown->getName();
                            $timeToArrival = $army->getMovement()->getEndTime() - $currentTime;
                            if ($timeToArrival < 0)
                                $timeToArrival = 0;
                            break;
                        }
                    }
                }

                if ($army->getGarrison())
                    $currentTown['garrison'] = $armyUnits;
                else
                    $currentTown['armies'][] = array(
                        'armyid' => $army->getId(),
                        'moving' => $army->getMoving(),
                        'units' => $armyUnits,
                        'destination' => $destination,
                        'timeToArrival' => $timeToArrival);
            }
            $resultArray[] = $currentTown;
        }
        return $resultArray;
    }

    /**
    * \brief returns true if the given clan has won the game
    * @param HRClan
    * @param HRGame
    * @return boolean
    */
    public function checkClanVictory(HRClan $clan, HRGame $game)
    {
        $r = $this->_em->createQuery("
                SELECT o.id
                FROM EIPHRBundle:HRTown t
                JOIN t.owner o
                WHERE t.game = :gameid
            ")
        ->setParameters(array(
                ':gameid' => $game->getId(),
            ))
        ->getArrayResult();

        $ids = array();
        foreach ($r as $key => $value)
            $ids[] = $value['id'];

        $r = $this->_em->createQuery("
                SELECT m FROM EIPHRBundle:HRClanMembers m
                LEFT JOIN EIPHRBundle:HRClan c WITH c.id = m.idClan
                WHERE c.idGame = :gameid
                AND m.idUser in (:ids)
            ")
        ->setParameters(array(':ids'=>$ids, ':gameid'=> $game->getId()))
        ->getArrayResult();

        if (count($r) < count($ids))
            return false;
        foreach ($r as $key => $value) {
            if ($value['idClan'] != $clan->getId())
                return false;
        }
        return true;
    }

    /**
    * \brief returns true if the given user has won the game
    * @param HRUser
    * @param HRGame
    * @return boolean
    */
    public function checkUserVictory(HRUser $user, HRGame $game)
    {
        $r = $this->_em->createQuery("
                SELECT COUNT(t.id)
                FROM EIPHRBundle:HRTown t
                JOIN t.owner u
                WHERE t.game = :gameid
                AND u.id != :userid
            ")
        ->setParameters(array(
                ':userid' => $user->getId(),
                ':gameid' => $game->getId()
            ))
        ->getSingleScalarResult();
        return $r == 0;
    }

}
