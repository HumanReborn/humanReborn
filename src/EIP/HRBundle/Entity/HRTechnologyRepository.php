<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRTechnologyRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRTechnologyRepository extends EntityRepository
{
    /**
     * Get technolofyScore
     * \brief return the technology's score of the $user on the $game
	 *
     * @param HRUser $user
     * @param HRGame $game
	 *
     * @return integer
     */
    public function getTechnologyScore($user, $game) {
        return $this->_em->createQuery("
            SELECT SUM(DISTINCT(s.rValue))
            FROM EIPHRBundle:HRTechnology t
            JOIN t.schema s
            WHERE t.user = :user
            AND t.game = :game
            ")
                ->setParameter(':user', $user->getId())
                ->setParameter(':game', $game->getId())
                ->getSingleScalarResult();
    }


     /**
     * Get getPlayerTechnologies
     * \brief return the technologyies owned by a player for a certain game
	 *
     * @param HRUser $user
     * @param HRGame $game
	 *
     * @return array of HRTechnology
     */
    public function getPlayerTechnologies($user, $game) {
        return $this->_em->createQuery("
            SELECT t,s
            FROM EIPHRBundle:HRTechnology t
            JOIN t.schema s
            WHERE t.user = :user
            AND t.game = :game
            ")
                ->setParameter(':user', $user->getId())
                ->setParameter(':game', $game->getId())
                ->getResult();
    }

    /**
     * \brief check if the technology is already known or being researched
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param integer $schemaid
     * @return boolean
     */
    public function isKnown(HRUser $user, HRGame $game, $schemaid) {
        return $this->_em->createQuery("
                SELECT 1 FROM EIPHRBundle:HRTechnologySchema s
                WHERE s.id = :schemaid AND (EXISTS
                    (
                        SELECT 1
                        FROM EIPHRBundle:HRTechnology t
                        WHERE t.user = :userid
                        AND t.game = :gameid
                        AND t.schema = s.id
                    ) OR EXISTS(
                        SELECT 1
                        FROM EIPHRBundle:HRTechnologyQueue t2
                        WHERE t2.user = :userid
                        AND t2.game = :gameid
                        AND t2.schema = s.id
                    )
                )
            ")
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' =>$game->getId(),
                    ':schemaid'=> $schemaid
                ))
                ->setMaxResults(1)
                ->getOneOrNullResult() != null;

    }

}
