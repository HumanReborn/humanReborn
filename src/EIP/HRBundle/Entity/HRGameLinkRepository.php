<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRGameLinkRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRGameLinkRepository extends EntityRepository
{
    /**
     * Get userGameLinks
     * \brief return a list of HRGameLink which matches with the $userID
	 *
     * @param int $userID
	 *
     * @return HRGameLink[]
     */
    public function getUserGameLinks($userID) {
        return $this->getEntityManager()
                ->createQuery("SELECT l  FROM EIPHRBundle:HRGameLink l  WHERE l.user = :userID")
                ->setParameter(':userID', $userID)
                ->getResult();
    }

    /**
     * Get gamesByUserId
     * \brief return a list of HRGame which matches with the $userID
	 *
     * @param int $userID
	 *
     * @return HRGame[]
     */
    public function getGamesByUserId($userID)
    {
        return $this->_em->createQuery("
                SELECT l, g
                FROM EIPHRBundle:HRGameLink l
                JOIN l.game g
                WHERE l.user = :userID
                ORDER BY g.name ASC
            ")->setParameter(':userID', $userID)
                ->getResult();
    }

    /**
     * Get resourcesFor
     * \brief return an array of resources for a user in a game
     *
     * @param int $userid
     * @param int $gameid
	 *
     * @return integer[]
     */
    public function getResourcesFor($userid, $gameid, $currentTime)
    {
        $gl = $this->_em->createQuery("
            SELECT gl,b,bs,h,s
            FROM EIPHRBundle:HRGameLink gl
            LEFT JOIN gl.buffs b
            LEFT JOIN b.schema bs
            JOIN gl.hero h
            JOIN h.schema s
            WHERE gl.user = :userid
            AND gl.game = :gameid
            ")
                ->setParameter(':userid', $userid)
                ->setParameter(':gameid', $gameid)
                ->getSingleResult();
        return $gl->getBuffedResources($currentTime);
    }

    /**
     * \brief return an array containing the sum of the different buffs in a game grouped by type
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param bigint $currentTime
     * @return array
     */
    public function getUserTotalBuffs(HRUser $user, HRGame $game, $currentTime)
    {
        $buffs = array(
            'attack' => 0,
            'armor' => 0,
            'hp' => 0
        );
        $gl = $this->_em->createQuery("
                    SELECT gl,b,bs,h,hs
                    FROM EIPHRBundle:HRGameLink gl
                    LEFT JOIN gl.buffs b
                    LEFT JOIN b.schema bs
                    JOIN gl.hero h
                    JOIN h.schema hs
                    WHERE gl.user = :userid AND gl.game = :gameid")
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId()
                ))->getSingleResult();
        foreach ($gl->getBuffs() as $b)
        {
            if ($b->isValid($currentTime) == false) continue;
            switch ($b->getSchema()->getType())
            {
                case \EIP\HRBundle\Entity\HRBuffSchema::ATTACK_ALL_TYPE: $key = 'attack'; break;
                case \EIP\HRBundle\Entity\HRBuffSchema::ARMOR_ALL_TYPE: $key = 'armor'; break;
                case \EIP\HRBundle\Entity\HRBuffSchema::HEALTH_ALL_TYPE: $key = 'hp'; break;
                default: $key = ''; break;
            }
            if (!$key || $key == '') continue;
            $buffs[$key] += $b->getSchema()->getValue();
        }
        $buffs['attack'] += $gl->getHero()->getTotalBonusAttack();
        $buffs['armor'] += $gl->getHero()->getTotalBonusArmor();
        $buffs['hp'] += $gl->getHero()->getTotalBonusHealth();
        $buffs['hero'] = $gl->getHero();
        return $buffs;
    }

    /**
     * \brief Returns the number of game a player has joined based on its id
     * @param integer $userid
     * @return integer
     */
    public function getGamesNumber($userid) {
        return $this->_em->createQuery("
                SELECT COUNT(gl.id)
                FROM EIPHRBundle:HRGameLink gl
                WHERE gl.user = :userid
            ")
                ->setParameter(':userid', $userid)
                ->getSingleScalarResult();
    }

    /**
    * \brief when a city is captured, updates the resource gain for each player
    *
    * @param HRGame
    * @param HRUser
    * @param HRUser
    * @param array
    *
    */
    public function updateResourcesGain(HRGame $game, HRUser $winner, HRUser $loser, array $resources)
    {
        $gls = $this->_em->createQuery("
                                SELECT gl
                                FROM EIPHRBundle:HRGameLink gl
                                WHERE gl.game = :gameid
                                AND (
                                     gl.user = :winnerid OR gl.user = :loserid
                                    )
                                ")
            ->setParameters(array(
                            ':gameid' => $game->getId(),
                            ':winnerid' => $winner->getId(),
                            ':loserid' => $loser->getId()
                            ))
            ->getResult();
        $winnerGl = ($gls[0]->getUser()->getId() === $winner->getId()) ? $gls[0] : $gls[1];
        $loserGl = ($winnerGl->getId() === $gls[0]) ? $gls[1] : $gls[0];
        foreach ($resources as $key => $value) {
            $winnerGl->addResource($key, $value);
            $loserGl->removeResource($key, $value);
        }
        // flushed at the end of checkAll::movement completion method
    }
}
