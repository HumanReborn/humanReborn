<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HRHeroRepository
 *
 * \brief Repository for the HRHero entity
 */
class HRHeroRepository extends EntityRepository
{
    /**
     * \brief returns the hero and  its schema for a user and a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRHero
     */
    public function getHeroInformations(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery("
                SELECT h, s, i, isc
                FROM EIPHRBundle:HRHero h
                JOIN h.schema s
                LEFT JOIN h.items i
                LEFT JOIN i.schema isc
                WHERE h.user = :userid
                AND h.game = :gameid
            ")
            ->setParameter(':userid', $user->getId())
            ->setParameter(':gameid', $game->getId())
            ->getSingleResult();
    }

    /**
    * \brief returns information player's hero for a certain game
    *
    */
    public function getMapInfoFor($idArray, $gameid)
    {
        return $this->_em->createQuery("
                SELECT h.level level, s.name heroName, u.id userId
                FROM EIPHRBundle:HRHero h
                JOIN h.schema s
                JOIN h.user u
                WHERE h.user in (:idArray)
                AND h.game = :gameid
            ")
            ->setParameter(':idArray', $idArray)
            ->setParameter(':gameid', $gameid)
            ->getArrayResult();
    }

}
