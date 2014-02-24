<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HRBuffRepository
 *
 * \brief Repository for the HRBuff entity
 */
class HRBuffRepository extends EntityRepository
{

    /**
     * \brief get a list of the currently active buffs in a game for a player
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRBuff[]
     */
    public function getCurrentBuffs(HRUser $user, HRGame $game){
        return $this->_em->createQuery("
                SELECT b,s
                FROM EIPHRBundle:HRBuff b
                JOIN b.schema s
                JOIN b.gameLink gl
                WHERE gl.user = :userid
                AND gl.game = :gameid
                AND (b.validUntil >= :currentTime
                    OR s.permanent = 1)
                ORDER BY b.validUntil ASC
            ")
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                    ':currentTime' => time()
                ))
                ->getResult();
    }

    public function getTimeReduction(HRUser $user, HRGame $game, $bufftype) {
        return $this->_em->createQuery("
                SELECT SUM(bs.value)
                FROM EIPHRBundle:HRBuff b
                JOIN b.schema bs
                JOIN b.gameLink gl
                WHERE bs.type = :buildingType
                AND gl.game = :gameid
                AND gl.user = :userid
                AND (b.validUntil >= :currentTime
                    OR bs.permanent = 1)
            ")
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                    ':buildingType' => $bufftype,
                    ':currentTime' => time()
                ))
                ->getSingleScalarResult();
    }

}
