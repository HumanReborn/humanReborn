<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class LCTokenRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class LCTokenRepository extends EntityRepository
{
    /**
     * Fetch validToken
     * \brief return a LCToken which matches with $token
	 *
     * @param HRToken $token
	 *
     * @return HRToken
     */
    public function findValidToken($token) {
        return $this->_em->createQuery("
            SELECT t
            FROM EIPHRBundle:LCToken t
            WHERE t.key = :token
            AND t.expire > :now
            ")
                ->setParameter(':token', $token)
                ->setParameter(':now', new \DateTime("now"))
                ->getOneOrNullResult();
    }
}
