<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRBuildingRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRBuildingRepository extends EntityRepository
{

    /**
     * Fetch townBuildings
     * \brief return a list of HRBuilding which matches with the HRTown $town
     * @param HRTown $town
     * @return HRBuilding[]
     */
    public function fetchTownBuildings(HRTown $town) {
        return $this->_em->createQuery(
                "SELECT b, s
                    FROM EIPHRBundle:HRBuilding b
                    JOIN b.schema s
                    WHERE b.town = :townid
                    ORDER BY b.id ASC"
                )
                ->setParameter(':townid', $town->getId())
                ->getResult();
    }

    /**
     * Get buildingScore
     * \brief return the building's score of the town $town
     * @param HRTown $town
     * @return integer
     */
    public function getBuildingScore(HRTown $town) {
        return $this->_em->createQuery("
            SELECT SUM(DISTINCT(s.rValue))
            FROM EIPHRBundle:HRBuilding b
            JOIN b.schema s
            WHERE b.town = :town
            ")
                ->setParameter(':town', $town->getId())
                ->getSingleScalarResult();
    }

    /**
     *  \brief Returns the global building score for a player and a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return integer
     */
    public function getGlobalBuildingScore(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery("
             SELECT SUM(DISTINCT(s.rValue))
             FROM EIPHRBundle:HRBuilding b
             JOIN b.schema s
             JOIN b.town t
             WHERE t.game = :game
             AND t.owner = :user
            ")
                ->setParameter(":user", $user->getId())
                ->setParameter(":game", $game->getId())
                ->getSingleScalarResult();
    }

}
