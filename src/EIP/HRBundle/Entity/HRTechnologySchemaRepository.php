<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRTechnologySchemaRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRTechnologySchemaRepository extends EntityRepository
{
    /**
     * \brief returns the list of the technologyschema owned by the player in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRTechnologySchema[]
     */
    public function getPlayerTechnologySchemas(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery("
                    SELECT s from EIPHRBundle:HRTechnologySchema s
                    JOIN EIPHRBundle:HRTechnology t WITH t.schema = s.id
                    WHERE t.user = :userid AND t.game = :gameid
                ")
                ->setParameters(array(
                    ':userid'=>$user->getId(),
                    ':gameid' => $game->getId()))
                ->getResult();
    }
}
