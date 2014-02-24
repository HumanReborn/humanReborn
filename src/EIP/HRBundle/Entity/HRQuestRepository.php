<?php

namespace EIP\HRBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * \brief EntityRepository for HRQuest
 */
class HRQuestRepository extends EntityRepository
{
    /**
     * \brief Returns the current quests for a player in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRQuest[]
     */
    public function getCurrentQuests(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery('
                SELECT q,s
                FROM EIPHRBundle:HRQuest q
                JOIN q.schema s
                WHERE q.user = :userid
                AND q.game = :gameid
                AND q.state = :ongoing
            ')
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                    ':ongoing' => HRQuest::STATE_ONGOING
                ))
                ->getResult();
    }

    /**
     * \bief Returns the completed quests for a player in a game
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @return HRQuest[]
     */
    public function getCompletedQuests(HRUser $user, HRGame $game)
    {
        return $this->_em->createQuery('
                SELECT q,s
                FROM EIPHRBundle:HRQuest q
                JOIN q.schema s
                WHERE q.user = :userid
                AND q.game = :gameid
                AND q.state != :finished
            ')
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                    ':finished' => HRQuest::STATE_ONGOING
                ))
                ->getResult();
    }

    /**
     * \brief Returns a quest for a user in a game based on its schemaID
     * @param \EIP\HRBundle\Entity\HRUser $user
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param type $schemaid
     * @return HRQuest
     */
    public function getQuestBySchemaID(HRUser $user, HRGame $game, $schemaid) {
        return $this->_em->createQuery('
                SELECT q, qs
                FROM EIPHRBundle:HRQuest q
                JOIN q.schema qs
                WHERE q.user = :userid
                AND q.schema = :schemaid
                AND q.game = :gameid
            ')
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                    ':schemaid' => $schemaid
                ))
                ->getOneOrNullResult();
    }

    /**
     * \brief change the state of the quest for all the user in a game and remove a quest from questgamelinks, make it unavailable to join
     * @param \EIP\HRBundle\Entity\HRGame $game
     * @param \EIP\HRBundle\Entity\HRQuestSchema $schema
     */
    public function desactivateQuest(HRGame $game, HRQuestSchema $schema) {
        $this->_em->createQuery('
                UPDATE q
                FROM EIPHRBundle:HRQuest q
                SET q.state = :newState
                WHERE q.game = :gameid
                AND q.schema = :schemaid
                AND q.state = :ongoing
            ')
                ->execute(array(
                    ':newState' => HRQuest::STATE_FINISHED,
                    ':gameid' => $game->getId(),
                    ':schemaid' => $schema->getId(),
                    ':ongoing' => HRQuest::STATE_ONGOING
                ));
        //
        $this->_em->createQuery('
                DELETE ql
                FROM EIPHRBundle:HRQuestGameLink ql
                WHERE ql.game = :gameid
                AND ql.questSchema = :schemaid
            ')
                ->setMaxResults(1)
                ->execute(array(
                    ':gameid' => $game->getId(),
                    ':schemaid' => $schema->getId()
                ));
    }

    public function getQuestFor(HRUser $user, HRGame $game, $type, $state)
    {
        return $this->_em->createQuery('
                SELECT q,qs
                FROM EIPHRBundle:HRQuest q
                JOIN q.schema qs
                WHERE q.user = :userid
                AND q.game = :gameid
                AND q.state = :state
                AND qs.type = :type
                GROUP BY q.user
            ')
                ->setParameters(array(
                    ':userid' => $user->getId(),
                    ':gameid' => $game->getId(),
                    ':state'  => $state,
                    ':type' => $type
                ))
                ->getResult();
    }

}
