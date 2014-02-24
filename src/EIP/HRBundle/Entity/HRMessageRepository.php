<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class HRMessageRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRMessageRepository extends EntityRepository
{
    /**
     * Fetch all
     * \brief return a list which contains all the HRMessage
	 *
     * @return HRMessage[]
     */
    public function fetchAll() {
        return $this->_em->createQuery("
                    SELECT m,s,r
                    FROM EIPHRBundle:HRMessage m
                    JOIN m.sender s
                    JOIN m.receiver r
                    ORDER BY m.id ASC
                ")->getResult();
    }

    /**
     * Fetch inMessage
     * \brief return a list of HRMessage which matches with the $userid
	 *
     * @param int $userid
     * @param HRMessage[] $fetchAll
	 *
     * @return HRMessage[]
     */
    public function fetchInMessages($userid, $fetchAll) {
        $query = $this->_em->createQueryBuilder()
        ->select('m')
        ->addSelect('s')
        ->from('EIPHRBundle:HRMessage', 'm')
        ->join('m.sender', 's')
        ->join('m.receiver', 'r')
        ->where('r.id = :userid')
        ->orderBy("m.sentOn", "DESC")
        ->setParameter(':userid', $userid);

        if (!$fetchAll)
            $query->setMaxResults (9);
        return $query->getQuery()->getResult();
    }

    /**
     * Fetch outMessage
     * \brief return a list of HRMessage which matches with the $userid
	 *
     * @param int $userid
     * @param HRMessage[] $fetchAll
	 *
     * @return HRMessage[]
     */
    public function fetchOutMessages($userid, $fetchAll) {
        $query = $this->_em->createQueryBuilder()
                ->select('m')
                ->addSelect('r')
                ->from('EIPHRBundle:HRMessage', 'm')
                ->join('m.sender', 's')
                ->join('m.receiver', 'r')
                ->where('s.id = :userid')
                ->orderBy("m.sentOn", "DESC")
                ->setParameter(':userid', $userid);

        if (!$fetchAll)
            $query->setMaxResults (9);
        return $query->getQuery()->getResult();
    }
}
