<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \class ChatTokenRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
 
class ChatTokenRepository extends EntityRepository
{
    /**
     * Get token
     * \brief return a value that match with the $token value
     * @return ChatToken
     */
    public function getToken($token)
    {
            return $this->_em->createQuery("
                SELECT ct, u, g
                FROM EIPHRBundle:ChatToken ct
                JOIN ct.user u
                JOIN ct.game g
                WHERE ct.value = :value
                AND ct.validUntil > :ctime
            ")
                ->setParameter(':value', $token)
                ->setParameter(':ctime', time())
                ->getOneOrNullResult();
    }
}
