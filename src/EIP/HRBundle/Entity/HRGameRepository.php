<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRGameRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRGameRepository extends EntityRepository
{
    /**
     * Get gameList
     * \brief return a list of HRGame which contains all the HRGame
	 *
     * @return HRGame[]
     */
    public function getGameList() {
        return $this->getEntityManager()
                        ->createQuery("SELECT g FROM EIPHRBundle:HRGame g WHERE g.private = false ORDER BY g.name ASC")
                        ->getResult();
    }

    /**
    *   \brief returns the test game for a player, or null if he doesnt have one
    *  @return HRGame?
    */
    public function getTestGameForUser(HRUser $user) {
        return $this->_em->createQuery("SELECT g FROM EIPHRBundle:HRGame g
                                       JOIN EIPHRBundle:HRGameLink gl
                                       WITH gl.game = g.id
                                       WHERE g.private = true
                                       AND gl.user = :userid")
                        ->setParameter(':userid', $user->getId())
                        ->getOneOrNullResult();
    }

    /** \brief sets the games' status to HRGame::STATUS_OPEN when game.openedOn is reached
    */
    public function updateOpenedGamesList()
    {
        $this->_em->createQuery("
                                UPDATE EIPHRBundle:HRGame g
                                SET g.status = :opened
                                WHERE g.openedOn < :now
                                AND g.status = :can_signup
                                ")
                    ->setParameters(array(
                                    ':opened' => HRGame::STATUS_OPENED,
                                    ':can_signup' => HRGame::STATUS_CAN_SIGNUP,
                                    ':now' => new \DateTime()
                                    ))
                    ->execute();

    }
}
