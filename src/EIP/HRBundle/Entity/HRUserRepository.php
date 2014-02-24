<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * \class HRUserRepository
 * \brief Entity repository, see: http://symfony.com/doc/2.1/book/doctrine.html
 */
class HRUserRepository extends EntityRepository implements UserProviderInterface //implements  UserProviderInterface to enable login by username or email
{
    /**
     * Get userByUsername
     * \brief return a HRUser which matches with the $username
	 *
     * @param int $username
	 *
     * @return HRUser
     */
    public function loadUserByUsername($username) {
        return $this->getEntityManager()
                            ->createQuery('SELECT u FROM
                                                    EIPHRBundle:HRUser u
                                                    WHERE u.username = :username
                                                    OR u.email = :username')
                            ->setParameters(array(
                                          ':username' => $username
                                           ))
                            ->getOneOrNullResult();
    }

    /**
     * Fetch playerArmies
     * \brief return a HRUser which matches with $user->getUsername()
	 *
     * @param UserInterface $user
	 *
     * @return HRUser
     */
    public function refreshUser(\Symfony\Component\Security\Core\User\UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * supportsClass
     * \brief fonction required by symfony2
	 *
     * @param string $class
	 *
     * @return boolean
     */
    public function supportsClass($class) {
        return $class === 'EIP\HRBundle\Entity\HRUser';
    }

    /**
     * \brief Returns a user and its achievements based on its username
     * @param string $username
     * @return HRUser with achievements and associated schemas
     */
    public function getUserProfile($username) {
        return $this->_em->createQuery("
            SELECT u,ac,acs
            FROM EIPHRBundle:HRUser u
            LEFT JOIN u.achievements ac
            LEFT JOIN ac.schema acs
            WHERE u.username = :username
            ORDER BY acs.type ASC, ac.achievedOn ASC
        ")
                ->setParameter(':username', $username)
                ->getSingleResult();
    }

    /**
    * \brief Returns the HRUser used as neutral player for all HumanReborn games
    * @return HRUser
    */
    public function getNeutralPlayer() {
        return $this->_em->createQuery("
                SELECT u FROM EIPHRBundle:HRUser u WHERE u.username = :neutralPlayerUsername
            ")
        ->setParameter(':neutralPlayerUsername', 'HumanReborn')
        ->getSingleResult();
    }

    /**
    * \brief updates the locale attribute for a given user
    */
    public function updateLocaleForUser(HRUser $user, $loc){
        $this->_em->createQuery("UPDATE EIPHRBundle:HRUser u SET u.locale = :loc WHERE u.id = :userid")
        ->setParameters(array(':loc' => $loc, ':userid' => $user->getId()))
        ->setMaxResults(1)
        ->execute();
    }

    /**
    * \brief returns the list of the usernames
    * @return string[]
    */
    public function getUsernames()
    {
        return $this->_em->createQuery("
                SELECT u.username username
                FROM EIPHRBundle:HRUser u
            ")
        ->setMaxResults(512)
        ->getScalarResult();
    }

}
