<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \brief Repository for the HRNotification entity
 */
class HRNotificationRepository extends EntityRepository
{
    /**
     * \brief returns the last 10 HRNotifications for a player in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRNotification[]
     */
    public function getLastNotifications(HRUser $user, HRGame $game) {
        return $this->_em->createQuery("
            SELECT n
            FROM EIPHRBundle:HRNotification n
            WHERE n.game = :game
            AND n.user = :user
            ORDER BY n.time DESC
        ")->setParameters(array(
            ':user' => $user->getId(),
            ':game' => $game->getId()
        ))
                ->setMaxResults(10)
                ->getResult();
    }
    
    /**
     * \brief returns 40 HRNotifications for a player in a game, ordered by notification_time, with pagination
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param type $page
     * @return HRNotification[]
     */
    public function getNotifications(HRUser $user, HRGame $game, $page = 1) {
        return $this->_em->createQuery("
                SELECT n
                FROM EIPHRBundle:HRNotification n
                WHERE n.game = :game
                AND n.user = :user
                ORDER BY n.time DESC
            ")->setParameters(array(
                ':user' => $user->getId(),
                ':game' => $game->getId()
            ))
                ->setFirstResult(($page-1) * 40)
                ->setMaxResults(40)
                ->getResult();
    }
    
    /**
     * \brief returns the number of notification for a user in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return integer
     */
    public function getNotificationsCount(HRUser $user, HRGame $game) {
        return $this->_em->createQuery("
                SELECT COUNT(n.id)
                FROM EIPHRBundle:HRNotification n
                WHERE n.game = :game
                AND n.user = :user                
            ")->setParameters(array(
                ':user' => $user->getId(),
                ':game' => $game->getId()
            ))->getSingleScalarResult();
    }
}
