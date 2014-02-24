<?php

namespace EIP\HRBundle\Utils;

use EIP\HRBundle\Entity\HRNotification;
use EIP\HRBundle\Entity\HRQuestSchema;
use EIP\HRBundle\Entity\HRQuest;
use EIP\HRBundle\Entity\HRUser;
use EIP\HRBundle\Entity\HRArmy;
use EIP\HRBundle\Entity\HRTown;
use EIP\HRBundle\Entity\HRGame;
/**
 *	\class TestTools
 *	\brief regroup usefull static functions
*/
// to be renamed
class HRTools
{

    /**
    * \brief makes two armies fight
    * @param HRArmy
    * @param HRArmy
    * @param EntityManager
    * @param integer
    * @param Translator
    */
    public static function resolveFight(HRArmy $attacker,
                                        HRArmy $defender,
                                        \Doctrine\ORM\EntityManager $em,
                                        $currentTime,
                                        \Symfony\Component\Translation\Translator $tr)
    {
        $armies = array(
            $attacker->getUser()->getId() => $attacker->getUnitsCount(),
            $defender->getUser()->getId() => $defender->getUnitsCount()
        );


        $attackerBuffs = $em->getRepository('EIPHRBundle:HRGameLink')->getUserTotalBuffs($attacker->getUser(), $attacker->getGame(), $currentTime);
        $defenderBuffs = $em->getRepository('EIPHRBundle:HRGameLink')->getUserTotalBuffs($defender->getUser(), $defender->getGame(), $currentTime);

        $attacker->initializeUnitsTmpHp($attackerBuffs['hp']);
        $defender->initializeUnitsTmpHp($defenderBuffs['hp']);

        $nowDateTime = new \DateTime('@'.$currentTime); // @ -> create from timestamp

        while ($attacker->isDefeated() == false && $defender->isDefeated() == false)
        {
            $afs = $attacker->getFightingScore($attackerBuffs);
            $dfs = $defender->getFightingScore($defenderBuffs);
            $attacker->receiveDamage($dfs, $attackerBuffs);
            $defender->receiveDamage($afs, $defenderBuffs);
        }
        // check who's won
        if ($defender->isDefeated($em))
        {
            $winner = $attacker;
            $loser = $defender;
            $xpWon = $defender->getXpValue();
            $attackerBuffs['hero']->receiveXp($xpWon);
        }
        else
        {
            $winner = $defender;
            $loser = $attacker;
            $xpWon = $attacker->getXpValue();
            $defenderBuffs['hero']->receiveXp($xpWon);
        }

        $lostUnits = array(
                           'winner' => $winner->getLostUnits($em),
                           'loser' => $loser->getLostUnits($em)
                           );
        // generating the battle report
        $battleReport = \EIP\HRBundle\Entity\HRBattleReport::createBattleReport($attacker, $defender, $winner, $nowDateTime, $xpWon, $lostUnits, $armies);

        $em->persist($battleReport);
        // update achievements progression
        $winnerTotalDestroyedUnits = array_sum(array_values($lostUnits['loser']));
        $loserTotalDestroyedUnits = array_sum(array_values($lostUnits['winner']));
        $winner->getUser()->addToAchievementsProgression('nbUnitsDestroyed', $winnerTotalDestroyedUnits);
        $loser->getUser()->addToAchievementsProgression('nbUnitsDestroyed', $loserTotalDestroyedUnits);
        // update destroy quest if any
        self::updateDestroyUnitQuests($winner, $lostUnits['loser'], $em, $tr);
        self::updateDestroyUnitQuests($loser, $lostUnits['winner'], $em, $tr);

        //
        // sending notifications
        if (!$loser->isGarrison()) { // defender won
            $em->remove($loser);
            $contentStr = $tr->trans('army.lost', array('%armyid%' => $loser->getId(), '%town%' => $winner->getTown()->getName(),
                                     '%coords%' => '['.$winner->getTown()->getXCoord().':'.$winner->getTown()->getYCoord().']'), 'messages', $loser->getUser()->getLocale());
            $armyDestroyedNotification = HRNotification::createNotification(HRNotification::ALERT, $loser->getGame(), $loser->getUser(), $contentStr);
            $em->persist($armyDestroyedNotification);
        }
        else { // attacker won
            $contentStr = $tr->trans('town.lost', array('%townname%' => $loser->getTown()->getName(), '%username%' => $winner->getUser()->getUsername(),
                                     '%coords%' => '['.$loser->getTown()->getXCoord().':'.$loser->getTown()->getYCoord().']'), 'messages', $loser->getUser()->getLocale());
            $townLostNotification = HRNotification::createNotification(HRNotification::ALERT, $loser->getGame(), $loser->getUser(), $contentStr);
            $em->persist($townLostNotification);
        }

        if (!$winner->isGarrison()) { // attacker won
            $contentStr = $tr->trans('attack.success', array('%armyid%' => $winner->getId(), '%townname%' => $loser->getTown()->getName(),
                                     '%coords%' => '['.$loser->getTown()->getXCoord().':'.$loser->getTown()->getYCoord().']'), 'messages', $winner->getUser()->getLocale());
            $attackSuccessNotification = HRNotification::createNotification(HRNotification::SUCCESS, $winner->getGame(), $winner->getUser(), $contentStr);
            $em->persist($attackSuccessNotification);
        }
        else { // defender won
            $contentStr = $tr->trans('town.defended', array('%townname%' => $winner->getTown()->getName(), '%username%' => $loser->getUser()->getUsername(),
                                     '%coords%' => '['.$winner->getTown()->getXCoord().':'.$winner->getTown()->getYCoord().']'), 'messages', $winner->getUser()->getLocale());
            $townDefendedNotification = HRNotification::createNotification(HRNotification::ALERT, $winner->getGame(), $winner->getUser(), $contentStr);
            $em->persist($townDefendedNotification);
        }

        // transfer town ownership if defender lost
        if ($loser->isGarrison())
        {
            $town = $loser->getTown();
            $em->remove($loser);
            // reassign armies | cancel move toward this town
            self::reAssignArmies($loser->getTown(), $loser->getUser(), $em);
            $producedResources = $town->getProducedResourcesArray();
            $em->getRepository('EIPHRBundle:HRGameLink')->updateResourcesGain($town->getGame(),
                                                                              $winner->getUser(),
                                                                              $loser->getUser(),
                                                                              $producedResources);

            $em->getRepository('EIPHRBundle:HRBuildingQueue')->removeQueuedBuildingsFromTown($town);
            $town->setOwner($winner->getUser());
            $newGarrison = new HRARmy($winner->getUser(), $town->getGame(), $town);
            $newGarrison->setGarrison(true);
            $em->persist($newGarrison);
            $winner->setTown($town);
            // check if the game is over
            self::checkGameOver($winner, $em);
        }
    }

    /** \brief called by ResolveFight */
    private static function reAssignArmies(HRTown $town, HRUser $user, $em) {
        $x = $town->getXCoord();
        $y = $town->getYCoord();

        $towns = $em->getRepository('EIPHRBundle:HRTown')->findBy(array('owner' => $user->getId(), 'game' => $town->getGame()->getId()));
        $minDist = null;
        $nearestTown = null;
        foreach ($towns as $t) {
            if ($t->getId() === $town->getId()) continue;
            $xDist = abs($x - $t->getXCoord());
            $yDist = abs($y - $t->getYCoord());
            $dist = sqrt($x*$x + $y*$y);
            if ($minDist === null || $dist < $minDist)
            {
                $minDist = $dist;
                $nearestTown = $t;
            }
        }
        if ($nearestTown === null) {
            // check if enemy or enemy alliance controls the map
        }
        else {
            $em->getRepository('EIPHRBundle:HRArmy')->reAssignHostTownFor($town, $user, $nearestTown);
        }
    }

    /** \brief called by ResolveFight */
    private static function updateDestroyUnitQuests(\EIP\HRBundle\Entity\HRArmy $army, $lostUnits,
                                                    \Doctrine\ORM\EntityManager $em,
                                                    \Symfony\Component\Translation\Translator $tr)
    {
        $armyQuests = $em->getRepository('EIPHRBundle:HRQuest')->getQuestFor($army->getUser(), $army->getGame(), HRQuestSchema::DESTROY_UNIT, HRQuest::STATE_ONGOING);
        foreach ($armyQuests as $quest)
        {
            $data = $quest->getData();
            foreach ($lostUnits as $k => $v)
            {
                if (array_key_exists($k, $data))
                {
                    $data[$k] += $v;
                }
            }
            $quest->setData($data);
            if ($quest->isCompleted())
                self::completeQuest ($quest, $quest->getUser(), $quest->getGame(), null, $tr, $em);
        }
    }

    /** \brief called by ResolveFight */
    private static function checkGameOver(\EIP\HRBundle\Entity\HRArmy $winner,
                                   \Doctrine\ORM\EntityManager $em)
    {
        $winnerClan = $em->getRepository('EIPHRBundle:HRClan')->getUserClanByGame($winner->getUser(), $winner->getGame());
        if ($winnerClan) //  check if all the town in the game belong to this clan
        {
            if ($em->getRepository('EIPHRBundle:HRTown')->checkClanVictory($winnerClan, $winner->getGame()))
            {
                // clan victory
                $score = new \EIP\HRBundle\Entity\HRScore();
                $score->setWinnerClan($winnerClan);
                $score->setGame($winner->getGame());
                $em->persist($score);
                $score->getGame()->setStatus(HRGame::STATUS_CLOSED);
            }
        }
        // check if all the town belong to the user
        else if ($em->getRepository('EIPHRBundle:HRTown')->checkUserVictory($winner->getUser(), $winner->getGame()))
        {
            // user victory
            $score = new \EIP\HRBundle\Entity\HRScore();
            $score->setWinnerUser($winner->getUser());
            $score->setGame($winner->getGame());
            $em->persist($score);
            $score->getGame()->setStatus(HRGame::STATUS_CLOSED);
        }
    }


    /**
    * \brief completes a quest for a player/game
    * @param HRQuest
    * @param HRUser
    * @param HRGame
    * @param FlashBag
    * @param Translator
    * @param EntityManager
    */
    public static function completeQuest(HRQuest $q,
                                         \EIP\HRBundle\Entity\HRUser $user,
                                         \EIP\HRBundle\Entity\HRGame $game,
                                    $flashBag, // null sometimes ~
                                    \Symfony\Component\Translation\Translator $tr,
                                    \Doctrine\ORM\EntityManager $em)
    {
        // notification + flash message
        $qname = $tr->trans($q->getSchema()->getName(), array(), 'quests');
        $message = $tr->trans('quest.completed', array('%questName%' => $qname), 'quests', $user->getLocale());
        if ($flashBag != null)
            $flashBag->add('info', $message);
        $notification = HRNotification::createNotification(HRNotification::SUCCESS, $game, $user, $message);
        $em->persist($notification);
        // update achievements progression
        $user->addToAchievementsProgression('nbQuestsCompleted', 1);
        // give hero xp and items
        $hero = $em->getRepository('EIPHRBundle:HRHero')->getHeroInformations($user, $game);
        $hero->receiveXp($q->getSchema()->getXpReward());
        foreach ($q->getSchema()->getItemReward() as $itemReward) {
            $newItem = new \EIP\HRBundle\Entity\HRItem();
            $newItem->setSchema($itemReward);
            $newItem->setHero($hero);
            $em->persist($newItem);
            $hero->getItems()->add($newItem);
        }
        // check if schema.once and desactivate for all if need be
        if ($q->getSchema()->getOnce()) {
            $em->getRespository('EIPHRBundle:HRQuest')->desactivateQuest($game, $q->getSchema());
        }
    }

    /**
    * \brief recruit unit(s)
    * @param integer schemaid
    * @param integer townid
    * @param EntityManager
    * @param Translator
    * @param integer quantity
    */
    public static function recruitUnit($schemaid, $townid, $em, $tr, $quantity = 1) {
        $town = $em->getRepository('EIPHRBundle:HRTown')->find($townid);

        $user = $town->getOwner();
        $game = $town->getGame();

        // check if queue is full
        $qCpt = $em->getRepository('EIPHRBundle:HRUnitQueue')->getQueuedUnitsCount($user->getId(), $game->getId());
        if ($qCpt + $quantity >= \EIP\HRBundle\Entity\HRUnitQueue::MAX_QUEUE_SLOTS)
            return false;

        $schema = $em->getRepository('EIPHRBundle:HRUnitSchema')->find($schemaid);
        $gamelink = $em->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
                                                                            'user' => $user->getId(),
                                                                            'game' => $game->getId()
                                                                            ));
        $trainingTimeReduction = $em->getRepository('EIPHRBundle:HRBuff')->getTimeReduction($user, $game, \EIP\HRBundle\Entity\HRBuffSchema::TRAINING_TIME_TYPE);
        // checking if the requirements are fulfilled
        $errors = array();
        $technoScore = $em->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $buildingScore = $em->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town);
        if (!$schema->checkBuildingRequirement($buildingScore))
            $errors[] = $tr->trans('building.requirement.not.fulfilled');
        if (!$schema->checkTechnologyRequirement($technoScore))
            $errors[] = $tr->trans('technology.requirement.not.fulfilled');

        // if no previous error, check if the player has enough resources
        if (!$gamelink->canBuy($schema, $quantity))
            $errors[] = $tr->trans('resources.not.enough');

        // insert the new unit in the unit queue
        if (count($errors) == 0)
        {
            $army = $em->getRepository('EIPHRBundle:HRArmy')->findOneBy(array(
                'town' => $town->getId(),
                'garrison' => true,
            ));
            $gamelink->Buy($schema, $quantity);
            $startTime = $em->getRepository('EIPHRBundle:HRUnitQueue')->fetchLastUnitCompletionTime($townid);
            foreach (range(1, $quantity) as $i) {
                $queuedUnit = new \EIP\HRBundle\Entity\HRUnitQueue($schema, $army, $startTime);
                $queuedUnit->applyTrainingTimeReduction($trainingTimeReduction);
                $em->persist($queuedUnit);
                $startTime = $queuedUnit->getEndTime();
            }
            $em->flush();
            return true;
        }
        return false;
    }

}