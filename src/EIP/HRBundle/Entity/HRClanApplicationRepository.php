<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * HRClanApplicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HRClanApplicationRepository extends EntityRepository
{
    
    public function isAlreadyApplicate($userId, $idGame) {
        $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('a')
                ->from('EIPHRBundle:HRClanApplication', 'a')
                ->leftjoin('EIPHRBundle:HRClan', 'c', 'WITH', 'c.id = a.idClan')
                ->where('a.idUser = :idUser AND a.pending = 0 AND c.idGame = :idGame')
                ->setParameter(':idUser', $userId)->setParameter(':idGame', $idGame);
    
        $res = $qb->getQuery()->getResult();
        if ($res)
            return true;
        else
            return false;
    }
    
    public function getAllApply($idClan) {
        $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('a.id as id, a.pending as pending, a.message as message, u.username as name')->from('EIPHRBundle:HRClanApplication', 'a')
                ->leftJoin('EIPHRBundle:HRUser', 'u', 'WITH', 'a.idUser = u.id')
                ->where('a.idClan = :idclan AND a.pending = 0')->setParameter(':idclan', $idClan);
        $res = $qb->getQuery()->getResult();
        
        return $res;
    }
    
    public function getOneApply($idApply){
        
        $qb = $this->getEntityManager()->createQueryBuilder()
                ->select('a')
                ->from('EIPHRBundle:HRClanApplication', 'a')
                ->where('a.id = :idapply')->setParameter(":idapply", $idApply);
        return $qb->getQuery()->getResult();
    }
}
