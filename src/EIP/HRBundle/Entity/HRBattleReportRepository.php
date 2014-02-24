<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HRBattleReportRepository
 * \brief Repository for the HRBattleReport
 */
class HRBattleReportRepository extends EntityRepository
{
    /**
     * \brief get the last 10 battle reports
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRBattleReport[]
     */
    public function getLastBattleReports(HRUser $user, HRGame $game) {
        return $this->_em->createQuery("
            SELECT br, att, def, t
            FROM EIPHRBundle:HRBattleReport br
            JOIN br.attacker att
            JOIN br.defender def
            LEFT JOIN br.town t
            
            WHERE (att.id = :userid OR def.id = :userid)
            AND br.game = :gameid
            ORDER BY br.time DESC
        ")    // LEFT JOIN br.quest q
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId()
                ))
                ->setMaxResults(10)
                ->getResult();
    }
    
    /**
     * \brief get a list 40 battle reports
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param integer $page
     * @return HRBattleReport[]
     */
    public function getBattleReports(HRUser $user, HRGame $game, $page = 1) {
        return $this->_em->createQuery("
                SELECT b
                FROM EIPHRBundle:HRBattleReport b
                WHERE b.game = :gameid
                AND (b.attacker = :userid OR b.defender = :userid)
                ORDER BY b.time DESC
            ")->setParameters(array(
                ':userid' => $user->getId(),
                ':gameid' => $game->getId()
            ))
                ->setFirstResult(($page-1) * 40)
                ->setMaxResults(40)
                ->getResult();
    }
    
    /**
     * \brief get the number of battle reports for a user and a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return integer
     */
    public function getBattleReportsCount(HRUser $user, HRGame $game) {
        return $this->_em->createQuery("
                SELECT COUNT(b.id)
                FROM EIPHRBundle:HRBattleReport b
                WHERE b.game = :gameid
                AND (b.attacker = :userid OR b.defender = :userid)            
            ")->setParameters(array(
                ':userid' => $user->getId(),
                ':gameid' => $game->getId()
            ))->getSingleScalarResult();
    }
    
    /**
     * \brief Get the complete information about a battle report
     * @param type $reportid
     * @return HRBattleReport
     */
    public function getReportFullInfos($reportid)
    {
        return $this->_em->createQuery("
                SELECT r,t,w,a,d,g
                FROM EIPHRBundle:HRBattleReport r
                LEFT JOIN r.town t
                JOIN r.winner w
                JOIN r.attacker a
                JOIN r.defender d
                JOIN r.game g
                WHERE r.id = :reportid
            ")
                ->setParameter(':reportid', $reportid)
                ->getSingleResult();
    }
}
