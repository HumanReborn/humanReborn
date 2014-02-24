<?php

namespace EIP\HRBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EIP\HRBundle\Entity\HRGameLink;

/**
 * \class GameController
 * \brief GameController Description
 */
class GameController extends Controller {

    /**
     * gameAction
     * \brief display the available game list
     *
     * @return view
     */
    public function gamesAction() {
        $this->getRequest()->getSession()->set('user', $this->getUser());
        $this->getRequest()->getSession()->set('openQueue', false);

        $gameRepo = $this->getDoctrine()->getRepository("EIPHRBundle:HRGame");
        $linkRepo = $this->getDoctrine()->getRepository("EIPHRBundle:HRGameLink");

        $games = $gameRepo->getGameList();
        $links = $linkRepo->getGamesByUserId($this->getUser());
        $testGame = $gameRepo->getTestGameForUser($this->getUser());

        $joinedGamesIds = array();
        foreach($links as $l)
            $joinedGamesIds[] = $l->getGame()->getId();

        return parent::render("EIPHRBundle:Game:games.html.twig", array(
                    'games' => $games,
                    'links' => $links,
                    'joinedGamesIds' => $joinedGamesIds,
                    'testGame' => $testGame
        ));
    }

    /**
     * joinGameAction
     * \brief join the selected game and do a redirection
     *
     * @return view
     */
    public function joinGameAction($gameID) {
        $user = $this->getUser();
        $game = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($gameID);
        if (!$user || !$game)
            throw new \Exception("invalid gameID or userID");
        $exists = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findBy(array(
            'user' => $user,
            'game' => $game,
        ));
        if (!$exists) {
            return $this->redirect($this->generateUrl('hr_hero_selection', array('gameid' => $game->getId())));
        }
        return $this->redirect($this->generateUrl('hr_games'));
    }

    /**
     * selectedGameAction
     * \brief insert the selected game into the session and do a redirection
     *
     * @return view
     */
    public function selectGameAction($gameID) {
        $game = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($gameID);
        if (!$game)
            throw new \Exception("invalid game id");
        $link = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')
                ->findOneBy(array('game' => $game->getId(), 'user' => $this->getUser()->getId()));

        if (!$link)
            throw new \Exception("failed to get game-user link");

        if ($game->isOpened() === false)
            throw new \Exception("The game is not opened yet");

        $this->getRequest()->getSession()->set('game', $game);
        $locale = $this->getRequest()->getLocale();
        // changing user locale if required
        if ($locale != null) {
            if ($locale != $this->getUser()->getLocale()) {
                $this->getUser()->setLocale($this->getRequest()->getLocale());
                $this->getDoctrine()->getManager()->flush();
            }
        }
        return $this->redirect($this->generateUrl('hr_game_dashboard'));
    }

    /**
     * dashboardAction
     * \brief display the dashboard of the selected game
     *
     * @return view
     */
    public function dashboardAction() {
        $user = $this->getUser();
        $game = $this->getRequest()->getSession()->get('game');
        $notifications = $this->getDoctrine()->getRepository('EIPHRBundle:HRNotification')->getLastNotifications($user, $game);
        $battleReports = $this->getDoctrine()->getRepository('EIPHRBundle:HRBattleReport')->getLastBattleReports($user, $game);
        return $this->render('EIPHRBundle:Game:dashboard.html.twig', array(
                    'notifications' => $notifications,
                    'battleReports' => $battleReports,
        ));
    }

    /**
     * messagesAction
     * \brief display the user's messages for the current game
     *
     * @return view
     */
    public function messagesAction() {
        $user = $this->getUser();
        $message = new \EIP\HRBundle\Entity\HRMessage();
        $message->setSender($user);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRMessageType(false), $message, array(
            'em' => $this->getDoctrine()->getManager(),
        ));

        $handler = new \EIP\HRBundle\Form\HRMessageHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);
        if ($handler->process())
            ; // reload page in all cases ( success or fail )




// fetching messages
        // get 9 last sent and received messages
        $inMessages = $this->getDoctrine()->getRepository('EIPHRBundle:HRMessage')->fetchInMessages($user->getId(), false);
        $outMessages = $this->getDoctrine()->getRepository('EIPHRBundle:HRMessage')->fetchOutMessages($user->getId(), false);
        $unreadMessages = 0;
        foreach ($inMessages as $m) {
            if ($m->getRead() == false)
                $unreadMessages++;
        }
        return $this->render('EIPHRBundle:Game:messages.html.twig', array(
                    'inMessages' => $inMessages,
                    'outMessages' => $outMessages,
                    'unreadMessages' => $unreadMessages,
                    'form' => $form->createView(),
        ));
    }

    /**
     * readMessageAction
     * \brief set the selected message as read and do a redirection
     *
     * @return view
     */
    public function readMessageAction() {
        $request = $this->getRequest();
        $id = $request->request->get('msgID');
        $message = $this->getDoctrine()->getRepository('EIPHRBundle:HRMessage')->find($id);

        $user = $this->getUser();
        if ($message->getReceiver()->getId() == $user->getId())
        {
            $message->setRead(true);
            $this->getDoctrine()->getManager()->flush();
        }
        return new \Symfony\Component\HttpFoundation\Response(200);
    }

    /**
     * townsAction
     * \brief display the user's town
     *
     * @return view
     */
    public function townsAction() {
        $user = $this->getUser();
        $game = $this->getGame();
        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getTownsList($user, $game);
        return $this->render('EIPHRBundle:Game:towns.html.twig', array(
                    'towns' => $towns,
        ));
    }

    /**
     * townDetailAction
     * \brief display the informations of the selected user's town
     *
     * @return view
     */
    public function townDetailAction($townid) {
        $user = $this->getUser();
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array('id' => $townid, 'owner' => $user->getId()));
        if (!$town)
            throw new \Exception("cette ville ne vous appartient pas");
        $buildings = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->fetchTownBuildings($town);
        $queuedBuildings = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingQueue')->fetchTownQueuedBuildings($town);

        $buildingCount = count($buildings) + count($queuedBuildings);

        return $this->render('EIPHRBundle:Game:townDetail.html.twig', array(
                    'town' => $town,
                    'buildings' => $buildings,
                    'queuedBuildings' => $queuedBuildings,
                    'remaining_slots' => \EIP\HRBundle\Entity\HRTown::SLOT_PER_TOWN - $buildingCount,
        ));
    }

    /**
     * buildAction
     * \brief display the buildings which are available to build into the selected town
     *
     * @return view
     */
    public function buildAction($townid, $schemaid) {
        $user = $this->getUser();
        $game = $this->getGame();

        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array('id' => $townid, 'owner' => $user->getId()));
        if (!$town)
            throw new \Exception("cette ville ne vous appartient pas");

        $buildingTimeReduction = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuff')->getTimeReduction($user, $game, \EIP\HRBundle\Entity\HRBuffSchema::BUILDING_TIME_TYPE);

        $buildings = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->fetchTownBuildings($town);

        $buildingSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->findAll();
        $technologySchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();

        // techno score & building score
        $technoScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $buildingScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town);

        $schemaViews = array();
        $infos = array($buildingSchemas, $technologySchemas, null, $buildingScore, $technoScore);

        $selected_schema = 0;
        $schemaid = intval($schemaid);
        foreach ($buildingSchemas as $schema) {
            if ($schemaid && is_integer($schemaid) && $schemaid == $schema->getId())
                $selected_schema = $schemaid;
            $infos[2] = $schema;
            $schemaViews[] = \EIP\HRBundle\Entity\SchemaView::getSchemaViewFromSchema($infos);
        }

        if ($schemaid == 0)
            $schemaid = $buildingSchemas[0]->getId();

        return $this->render("EIPHRBundle:Game:build.html.twig", array(
                    'town' => $town,
                    'schemaViews' => $schemaViews,
                    'selectedSchemaID' => $schemaid,
                    'buildingTimeReduction' => $buildingTimeReduction
        ));
    }

    /**
     * buildSchemaAction
     * \brief display the success or failure of the building action
     *
     * @return view
     */
    public function buildSchemaAction($townid, $schemaid) {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->getUser();
        $game = $this->getGame($em);
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->find($schemaid);
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array('id' => $townid, 'owner' => $user->getId()));
        if (!$town)
            throw new \Exception("cette ville ne vous appartient pas");
        // get the userGameLink
        $ugl = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
            'user' => $user->getId(),
            'game' => $game->getId(),
        ));
        $buildingTimeReduction = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuff')->getTimeReduction($user, $game, \EIP\HRBundle\Entity\HRBuffSchema::BUILDING_TIME_TYPE);
        // check if the player meet the building requirement and technology requirement to build the schema
        $errors = array();
        $technoScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $buildingScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town);
        if (!$schema->checkBuildingRequirement($buildingScore))
            $errors[] = $this->get('translator')->trans('building.requirement.not.fulfilled');
        if (!$schema->checkTechnologyRequirement($technoScore))
            $errors[] = $this->get('translator')->trans('technology.requirement.not.fulfilled');

        // if no previous error, check if the player has enough resources
        if (!$ugl->canBuy($schema))
            $errors[] = $this->get('translator')->trans('resources.not.enough');

        // building number
        $buildings = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->fetchTownBuildings($town);
        $queuedBuildings = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingQueue')->fetchTownQueuedBuildings($town);
        $buildingCount = count($buildings) + count($queuedBuildings);

        // if town have enought space
        if ($buildingCount >= 10)
            $errors[] = $this->get('translator')->trans('space.not.enough');

        // insert the new batiment in the building queue
        if (count($errors) == 0) {
            $ugl->Buy($schema);
            $startTime = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingQueue')->fetchLastBuildingCompletionTime($townid);
            // ... MUST get the game from the entityManager or the persist operation will fail . . .
            //$tmpgame = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($game->getId());
            $queuedBuilding = new \EIP\HRBundle\Entity\HRBuildingQueue($schema, $user, $game, $town, $startTime); // $tmpgame
            $queuedBuilding->applyBuildingTimeReduction($buildingTimeReduction);
            $this->getDoctrine()->getManager()->persist($queuedBuilding);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirect($this->generateUrl('hr_town_detail', array('townid' => $townid)));
    }

    /**
     * \brief search for a town with free buildings slots, if found: redirects to that town with the right building schema selected, if not: display error message
     * @param type $schemaid
     * @return View
     */
    public function buildRequirementAction($schemaid) {
        $user = $this->getUser();
        $game = $this->getGame();
        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getPlayerTowns($user, $game);
        $town = null;
        foreach ($towns as $t) {
            if ($t->getFreeSlotsNumber() > 0) {
                $town = $t;
                break;
            }
        }
        if (!$town)
            return $this->render('EIPHRBundle:Game:noTownAvailable.html.twig');
        return $this->redirect($this->generateUrl('hr_build', array('townid' => $town->getId(), 'schemaid' => $schemaid)));
    }

    /**
    * \brief destroys a building based on the provided id
    * @param integer $buildingId
    */
    public function destroyBuildingAction($buildingId)
    {
        $game = $this->getGame();
        $building = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->find($buildingId);
        if (!$building)
            throw new \Doctrine\ORM\EntityNotFoundException();
        if ($building->getTown()->getOwner()->getId() != $this->getUser()->getId())
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        if ($building->getTown()->getGame()->getId() != $game->getId())
            throw new \Exception("The building does not belong to the current game");
        $em = $this->getDoctrine()->getManager();
        $town = $building->getTown();
        if ($building->getSchema()->isCollecting())
        {
            $gl = $em->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array('user' => $this->getUser()->getId(), 'game' => $this->getGame()->getId()));
            $collectRates = $building->getSchema()->getCollectRates();
            foreach ($collectRates as $key => $value)
            {
                $gl->removeResource($key, $value);
            }
        }
        $town->getBuildings()->removeElement($building);
        $em->remove($building);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_town_detail', array('townid' => $town->getId())));
    }

    /**
     * unitsAction
     * \brief display the units which are available in the user's town
     *
     * @return view
     */
    public function unitsAction() {
        $user = $this->getUser();
        $session = $this->getRequest()->getSession();
        $game = $this->getGame();

        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getFullInformation($user, $game);
        return $this->render('EIPHRBundle:Game:units.html.twig', array(
                    'towns' => $towns,
        ));
    }

    /**
     * addArmyAction
     * \brief Displays the page to create a new army into the $townid
     *
     * @return view
     */
    public function addArmyAction($townid) {
        //$user = $this->getDoctrine()->getRepository('EIPHRBundle:HRUser')->find($this->getUser()->getId()); // HRUser ou pas ? : d
        $user = $this->getUser();
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(arraY(
            'id' => $townid,
            'owner' => $user
        ));
        if (!$town)
            throw new \Exception("No town " . $townid . " found for user " . $user->getId());
        $garrison = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getTownGarrison($townid);
        if (!$garrison)
            throw new \Exception("No garrison found for town " . $town->getId());

        $garrisonContent = \EIP\HRBundle\Entity\HRArmy::getUnitsCountArray($garrison->getUnits());
        return $this->render('EIPHRBundle:Game:addArmy.html.twig', array(
                    'town' => $town,
                    'garrison' => $garrison,
                    'garrisonContent' => $garrisonContent,
        ));
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function addNewArmyAction() {
        try {
            $garrisonID = $this->getRequest()->request->get('garrisonID');
            $garrison = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')
                    ->findOneBy(array(
                'id' => $garrisonID,
                'user' => $this->getUser(),
                'garrison' => true
            ));
            if (!$garrison)
                throw new \Exception("No garrison " . $garrisonID . " for player " . $this->getUser()->getId());
            $units = $this->getRequest()->request->get('transferedUnits');
            $newArmy = new \EIP\HRBundle\Entity\HRArmy($this->getUser(), $garrison->getGame(), $garrison->getTown());
            // get units to transfer to the new army (schemaID and number)
            // then check if the garrison contains enough units to do so
            $toTransfer = array();
            foreach ($units as $u)
                $toTransfer[$u['schema']] = $u['number'];

            $garrisonContent = $garrison->getUnits();
            $garrisonUnitsCount = \EIP\HRBundle\Entity\HRArmy::getUnitsCountArray($garrisonContent);

            foreach ($garrisonUnitsCount as $schemaID => $infos) {
                if (!array_key_exists($schemaID, $toTransfer))
                    continue;
                if ($toTransfer[$schemaID] > $infos['number'])
                    throw new \Exception("Garrison only have " . $infos['number'] . " schema: " . $infos['name'] . " ; tried to transfer: " . $toTransfer[$schemaID]);
                //if here, the army contains enough schema where id = $schemaID to transfer
                $transferCpt = 0;
                foreach ($garrisonContent as $u) {
                    if ($transferCpt < $toTransfer[$schemaID] && $u->getSchema()->getId() == $schemaID) {
                        $u->setArmy($newArmy);
                        $transferCpt++;
                    }
                }
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($newArmy);
            $em->flush();
            return new \Symfony\Component\HttpFoundation\Response("ok");
        } catch (\Exception $e) {
            $logger = $this->get('logger');
            $logger->info($e->getMessage());
            return new \Symfony\Component\HttpFoundation\Response("lol");
        }
    }

    /**
     * \fn armyAction
     * \brief Display details about an army (content, merge and transfer options)
     * @param int $armyid
     * @return View
     * @throws \Exception if the army does not belong to the user
     */
    public function armyAction($armyid) {
        $game = $this->getGame();
        $user = $this->getUser();
        $army = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfosAndMovement($armyid);
        if ($army->getUser()->getId() != $user->getId() || $army->getGame()->getId() != $game->getId() || $army->getGarrison())
            throw new \Exception("Wrong game/user or is a garrison");
        $armyContent = \EIP\HRBundle\Entity\HRArmy::getUnitsCountArray($army->getUnits());
        $mergeableArmies = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getMergeableArmies($army);
        $targetTowns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getTargetTowns($user, $game);
        $allyTowns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getAllyTowns($user, $game);

        $destination = null;
        $reachTime = null;
        if ($army->getMoving() == true) {
            $destination = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')
                    ->getTownAt($game, $army->getMovement()->getToX(), $army->getMovement()->getToY());
            $destination = $destination == null ? '?' : $destination->getName() . ' [' . $destination->getXCoord() . ':' . $destination->getYCoord() . ']';
            $reachTime = $army->getMovement()->getEndTime() - time();
            $reachTime = $reachTime >= 0 ? $reachTime : 0;
        }

        return $this->render('EIPHRBundle:Game:army.html.twig', array(
                    'army' => $army,
                    'mergeableArmies' => $mergeableArmies,
                    'armyContent' => $armyContent,
                    'targetTowns' => $targetTowns,
                    'allyTowns' => $allyTowns,
                    'destination' => $destination,
                    'reachTime' => $reachTime
        ));
    }

    /**
     * \fn armyMergeAction
     * \brief transfers troops from giving army to receiving army and delete the giving army if it is not a garrison
     * @return redirect to hr_units
     * @throws \Exception if merging conditions are not met
     */
    public function armyMergeAction() {
        $em = $this->getDoctrine()->getManager();
        // get the two armies
        $givingArmyID = $this->getRequest()->request->get('givingArmyID');
        $receivingArmyID = $this->getRequest()->request->get('receivingArmyID');
        $givingArmy = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfos($givingArmyID);
        $receivingArmy = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfos($receivingArmyID);
        // check if the player / town/ movement state allow merging
        if (($givingArmy->getUser() != $receivingArmy->getUser()) || ($givingArmy->getTown() != $receivingArmy->getTown()))
            throw new \Exception("Problem encoutered while merging");
        // transfer the units
        foreach ($givingArmy->getUnits() as $unit) {
            $unit->setArmy($receivingArmy);
            $givingArmy->getUnits()->removeElement($unit);
            $receivingArmy->getUnits()->add($unit);
        }
        // delete the army if it is not a garrison
        if ($givingArmy->isGarrison() == false)
            $em->remove($givingArmy);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_units'));
    }

    /**
     * \fn armyContentAction
     * \brief return a table containing a list of the unit in a given army
     * @return View
     * @throws \Exception
     */
    public function armyContentAction() {
        $armyID = $this->getRequest()->request->get('armyid');
        $army = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfos($armyID);
        if ($army->getUser() != $this->getUser())
            throw new \Exception("This army does not belong to user " . $this->getUser()->getId());
        $armyContentArray = \EIP\HRBundle\Entity\HRArmy::getUnitsCountArray($army->getUnits());
        return $this->render('EIPHRBundle:Game:_armyContent.html.twig', array(
                    'armyContent' => $armyContentArray
        ));
    }

    /**
     * \brief transfers troop from an army to another
     * @return View
     * @throws \Exception
     */
    public function armyTransferAction() {
        $receivingArmyID = $this->getRequest()->request->get('transferArmyID');
        $givingArmyID = $this->getRequest()->request->get('givingArmyID');
        $givingArmy = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfos($givingArmyID);
        $receivingArmy = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfos($receivingArmyID);
        // faire une method canTransfer ?
        if ($givingArmy->getUser() != $this->getUser() || $receivingArmy->getUser() != $this->getUser() || $receivingArmy->getTown() != $givingArmy->getTown()) {
            throw new \Exception("Malicious");
        }
        $transferArray = $this->getRequest()->request->get("unitTypes");
        // transfer the troops
        foreach ($transferArray as $schemaID => $value) {
            $givingArmy->transferTroops($receivingArmy, $schemaID, $value);
        }
        // delete the giving army if empty
        $em = $this->getDoctrine()->getManager();
        if ($givingArmy->getUnits()->count() == 0)
            $em->remove($givingArmy);
        $em->flush();
        // redirect to units page if the army is destroyed, else to the army details
        if ($givingArmy->getUnits()->count() > 0)
            return $this->redirect($this->generateUrl('hr_army', array('armyid' => $givingArmyID)));
        return $this->redirect($this->generateUrl('hr_units'));
    }

    public function armyAttackAction() {
        $request = $this->getRequest()->request;
        $armyID = $request->get('armyID');
        $targetTownID = $request->get('targetTownID');
        $game = $this->getGame();
        $attackingArmy = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getArmyFullInfosAndMovement($armyID);
        $attackedTown = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'id' => $targetTownID,
            'game' => $game->getId()
        ));

        if (!$attackingArmy || !$attackedTown || $attackingArmy->getMoving() || ($attackingArmy->getGame() != $attackedTown->getGame()) || ($attackingArmy->getUser() == $attackedTown->getOwner())) {
            throw new \Exception("Malicious");
        }
        //handle attack
        $attackingArmy->attackTown($attackedTown);
        $armyContent = \EIP\HRBundle\Entity\HRArmy::getUnitsCountArray($attackingArmy->getUnits());
        $reachTime = ($attackingArmy->getMovement()->getEndTime() - time());
        $this->getDoctrine()->getManager()->flush();
        return $this->render('EIPHRBundle:Game:armyAttack.html.twig', array(
                    'attackingArmy' => $attackingArmy,
                    'attackedTown' => $attackedTown,
                    'armyContent' => $armyContent,
                    'reachTime' => $reachTime
        ));
    }

    /**
     * \brief Move an army to an allied town
     * @return View
     */
    public function armyMoveAction() {

        $r = $this->getRequest()->request;
        $armyid = $r->get('armyID');
        $allyTownID = $r->get('allyTownID');
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'game' => $this->getRequest()->getSession()->get('game'),
            'owner' => $this->getUser(),
            'id' => $allyTownID
        ));
        return $this->moveArmyAction($armyid, $town->getXCoord(), $town->getYCoord());
    }

    /**
     * recruitAction
     * \brief display the units which are available to train in the user's town
     *
     * @return view
     */
    public function recruitAction($townid, $schemaid) {
        $user = $this->getUser();
        $game = $this->getGame();
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'owner' => $user->getId(),
            'id' => $townid,
        ));

        $trainingTimeReduction = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuff')->getTimeReduction($user, $game, \EIP\HRBundle\Entity\HRBuffSchema::TRAINING_TIME_TYPE);

        $technoScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);
        $buildingScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->getBuildingScore($town);

        $unitSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->findAll();
        $buildingSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->findAll();
        $technologySchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();

        $schemaViews = array();
        $selected_schema = 0;
        $schemaid = intval($schemaid);

        $infos = array($buildingSchemas, $technologySchemas, null, $buildingScore, $technoScore);
        foreach ($unitSchemas as $schema) {
            if ($schemaid && is_integer($schemaid) && $schemaid == $schema->getId())
                $selected_schema = $schemaid;
            $infos[2] = $schema;
            $schemaViews[] = \EIP\HRBundle\Entity\SchemaView::getSchemaViewFromSchema($infos);
        }
        if ($schemaid == 0)
            $schemaid = $unitSchemas[0]->getId();

        return $this->render('EIPHRBundle:Game:recruit.html.twig', array(
                    'town' => $town,
                    'schemaViews' => $schemaViews,
                    'selectedSchemaID' => $schemaid,
                    'trainingTimeReduction' => $trainingTimeReduction
        ));
    }

    /**
     * recruitSchemaAction
     * \brief display the success or failure of the recruit action
     *
     * @return view
     */
    public function recruitSchemaAction($townid, $schemaid, $quantity) {
        $em = $this->getDoctrine()->getManager();
        if (\EIP\HRBundle\Utils\HRTools::recruitUnit($schemaid, $townid, $this->getDoctrine()->getManager(), $this->get('translator'), $quantity) === true) {
        // flash message
            $tr = $this->get('translator');
            $schemaName = $em->getRepository('EIPHRBundle:HRUnitSchema')->getSchemaName($schemaid);
            $unitName = $tr->trans($schemaName, array(), 'units');
            $msg = $tr->trans('unit.training', array('%unitname%' => $unitName));
            $this->get('session')->getFlashBag()->add('unit', $msg);
        }
        return $this->redirect($this->generateUrl('hr_recruit', array('townid' => $townid)));
    }

    /**
     * \brief fill a popup with the unit description
     * @return View
     */
    public function unitDetailAction() {
        $unitSchemaID = $this->getRequest()->request->get('schemaid');
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->getDescription($unitSchemaID);
        return $this->render('EIPHRBundle:Game:_unitDetail.html.twig', array(
                    'schema' => $schema,
        ));
    }

    //////////////
    // Technolgy
    /////////////

    /**
     * \brief Main page for the management of technologies for a  player in a game
     *
     * @return view
     */
    public function technologyAction($schemaid) {
        $user = $this->getUser();
        $game = $this->getGame();
        $ownedTechnologies = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->getPlayerTechnologySchemas($user, $game);
        $queuedTechnologies = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologyQueue')->fetchUserQueue($user->getId(), $game->getId());
        return $this->render('EIPHRBundle:Game:technology.html.twig', array(
                    'schemaid' => $schemaid,
                    'ownedTechnologies' => $ownedTechnologies,
                    'queuedTechnologies' => $queuedTechnologies
        ));
    }

    /**
     * \brief returns the technology schemaviews as json for a user/game
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function technologySchemaViewsAction() {
        $user = $this->getUser();
        $game = $this->getGame();
        $tr = $this->get('translator');
        // creating the schemaview
        $buildingSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->findAll();
        $technologySchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();
        $playerBuildingScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->getGlobalBuildingScore($user, $game);
        $playerTechnologyScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);


        $infos = array($buildingSchemas, $technologySchemas, null, $playerBuildingScore, $playerTechnologyScore);
        foreach ($technologySchemas as $schema) {
            $infos[2] = $schema;
            $sv = (\EIP\HRBundle\Entity\SchemaView::getSchemaViewFromSchema($infos));
            $name = $tr->trans($sv->getSchema()->getName(), array(), 'technologies');
            $desc = $tr->trans($sv->getSchema()->getDescription(), array(), 'technologies');
            $sv->getSchema()->setName($name);
            $sv->getSchema()->setDescription($desc);
            foreach (array('buildings' => $sv->getBuildingErrors(), 'technologies' => $sv->getTechnologyErrors()) as $trFile => $errors) {
                if ($errors) {
                    foreach ($errors as $e) {
                        $name = $tr->trans($e->getName(), array(), $trFile);
                        $e->setName($name);
                    }
                }
            }
            $schemaViews[] = $sv;
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse($schemaViews);
    }

    /**
     * \brief Handles the purchase of a technology
     *
     * @return view
     */
    public function researchStartAction($id) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $this->getGame($em);
        $gameLink = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
            'user' => $user->getId(),
            'game' => $game->getId()
        ));
        if ($this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->isKnown($user, $game, $id)) {
            $this->get('session')->getFlashBag()->add(
                    'info', 'technology.known'
            );
            return $this->redirect($this->generateUrl('hr_technology'));
        }

        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->find($id);
        // check the building and technology requirements for the schema
        $buildingSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->findAll();
        $technologySchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();
        $playerBuildingScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuilding')->getGlobalBuildingScore($user, $game);
        $playerTechnologyScore = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->getTechnologyScore($user, $game);

        $infos = array($buildingSchemas, $technologySchemas, $schema, $playerBuildingScore, $playerTechnologyScore);
        $schemaView = \EIP\HRBundle\Entity\SchemaView::getSchemaViewFromSchema($infos);

        if ($schemaView->getAvailable() == false)
            return $this->render('EIPHRBundle:Game:researchImpossible.html.twig', array(
                        'schemaView' => $schemaView
            ));

        if ($gameLink->canBuy($schema)) {
            $gameLink->buy($schema);
            // add to queue
            $startTime = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologyQueue')
                    ->fetchLastTechnologyCompletionTime($game->getId(), $user->getId());
            $queuedTechnology = new \EIP\HRBundle\Entity\HRTechnologyQueue($schema, $user, $game, $startTime);
            $this->getDoctrine()->getManager()->persist($queuedTechnology);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($this->generateUrl('hr_technology_research_started', array('schemaid' => $schema->getId())));
        }
        // not enough resources
        return $this->render('EIPHRBundle:Game:notEnoughResources', array(
                    'count' => 1,
                    'schema' => $schema
        ));
    }

    /**
     * \brief Page displayed once the research for a technology has started
     *
     * @return view
     */
    public function researchStartedAction($schemaid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->find($schemaid);
        return $this->render('EIPHRBundle:Game:researchStarted.html.twig', array('schema' => $schema));
    }

    // render functions

    /**
     * updateResources
     * \brief update the user's resources
     *
     * @return array
     */
    protected function updateResources($currentTime) {
        $session = $this->getRequest()->getSession();
        if (!$session->has('user') || !$session->has('game'))
            return null;
        return $resources = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')
                ->getResourcesFor($this->getUser()->getId(), $session->get('game')->getId(), $currentTime);
    }

    /**
     * getCurrentTown
     * \brief get the current town of the user
     */
    protected function getCurrentTown(array &$parameters) {
        if (!array_key_exists('town', $parameters)) {
            $parameters['town'] = new \EIP\HRBundle\Entity\HRTown();
            $parameters['town']->setName("");
        }
    }

    /**
     * getTownsInformations
     * \brief get the towns information of the user
     */
    protected function getTownsInformations(array &$parameters) {
        $session = $this->getRequest()->getSession();
        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findBy(array(
            'owner' => $this->getUser()->getId(),
            'game' => $session->get('game')->getId(),
        ));
        $parameters['allTowns'] = $towns;
    }

    /**
     * getBuildingQueueInformations
     * \brief get the building queue and display it into the dashboard
     */
    protected function getBuildingQueueInformations(array &$parameters) {
        $session = $this->getRequest()->getSession();
        $buildingQueue = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingQueue')->fetchUserQueue(
                $this->getUser()->getId(), $session->get('game')
        );
        $parameters['layoutBuildingQueue'] = $buildingQueue;
    }

    /**
     * getUnitQueueInformations
     * \brief get the unit queue and display it into the dashboard
     */
    protected function getUnitQueueInformations(array &$parameters) {
        $session = $this->getRequest()->getSession();
        $unitQueue = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitQueue')->fetchUserQueue(
                $this->getUser()->getId(), $session->get('game')
        );
        $parameters['layoutUnitQueue'] = $unitQueue;
    }

    /**
     * getTechnologyQueueInformations
     * \brief get the technology queue and display it into the dashboard
     */
    protected function getTechnologyQueueInformations(array &$parameters) {
        $session = $this->getRequest()->getSession();
        $unitQueue = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologyQueue')->fetchUserQueue(
                $this->getUser()->getId(), $session->get('game')
        );
        $parameters['layoutTechnologyQueue'] = $unitQueue;
    }

    /**
     * render
     * \brief used after each action for rendering the view
     * @return view
     */
    public function render($view, array $parameters = array(), \Symfony\Component\HttpFoundation\Response $response = null) {
        $t = time();
        $resources = $this->updateResources($t);
        if ($resources != null) {
            $parameters['currentTime'] = $t;
            $parameters['layoutResources'] = $resources;
            $this->getBuildingQueueInformations($parameters);
            $this->getUnitQueueInformations($parameters);
            $this->getTechnologyQueueInformations($parameters);
        }
        return parent::render($view, $parameters, $response);
    }

    /////////////////////
    // HERO
    /////////////////////

    /**
     * heroSelectionAction
     * \brief when the player choose a hero, he have to confirm his selection
     */
    public function heroSelectionAction($gameid) {
        $game = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($gameid);
        $heroSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRHeroSchema')->findAll();
        return parent::render('EIPHRBundle:Game:heroSelection.html.twig', array(
                    'game' => $game,
                    'heroschemas' => $heroSchemas
        ));
    }

    /**
     * heroConfirmationSelectionAction
     * \brief the selection has been confirmed and the player is redirected
     */
    public function heroConfirmSelectionAction($heroid, $gameid) {
        $heroSchema = $this->getDoctrine()->getRepository('EIPHRBundle:HRHeroSchema')->find($heroid);
        $game = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($gameid);
        return parent::render('EIPHRBundle:Game:heroConfirmSelection.html.twig', array(
                    'heroschema' => $heroSchema,
                    'game' => $game
        ));
    }

    /**
     * heroSelectionConfirmedAction
     * \brief create the hero into the database
     */
    public function heroSelectionConfirmedAction($heroid, $gameid) {
        $hero = new \EIP\HRBundle\Entity\HRHero();
        $user = $this->getUser();
        $game = $this->getDoctrine()
                        ->getRepository('EIPHRBundle:HRGame')->find($gameid);
        $heroSchema = $this->getDoctrine()
                        ->getRepository('EIPHRBundle:HRHeroSchema')->find($heroid);

        if ($this->getDoctrine()->getRepository('EIPHRBundle:HRHero')->findOneBy(array(
                    'user' => $user->getId(),
                    'game' => $game->getId()
                ))) {
            throw new \EIP\HRBundle\Utils\HRUserException("a hero already exists for user " . $user->getId() . " and game: " . $gameid);
        }
        $em = $this->getDoctrine()->getManager();
        $gl = new HRGameLink();
        $gl->setUser($user);
        $gl->setGame($game);
        $em->persist($gl);

        $hero->setSchema($heroSchema);
        $hero->setLevel(1);
        $hero->setGame($game);
        $hero->setUser($user);
        $em->persist($hero);
        $gl->setHero($hero);

        $user->addToAchievementsProgression('nbGames', 1);

        $em->flush();
        return $this->redirect($this->generateUrl('hr_games'));
    }

    /**
     * heroSelectionAction
     * \brief when entering into a game fot the 1st time, the player must choose a hero
     */
    public function heroAction() {
        $user = $this->getUser();
        $game = $this->getGame();
        $hero = $this->getDoctrine()->getRepository('EIPHRBundle:HRHero')->getHeroInformations($user, $game);
        return $this->render('EIPHRBundle:Game:hero.html.twig', array(
                    'hero' => $hero,
        ));
    }

    /**
     * \brief use an item
     * @return View
     */
    public function useItemAction($itemid) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $this->getGame($em);
        $hero = $this->getDoctrine()->getRepository('EIPHRBundle:HRHero')->findOneBy(array(
            'user' => $user,
            'game' => $game,
        ));
        $item = $this->getDoctrine()->getRepository('EIPHRBundle:HRItem')->getItemInfos($itemid, $hero);
        // check the effect of the item : buff, resource gain, free troops ?
        if ($item->givesBuff()) {
            $gameLink = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
                'user' => $user,
                'game' => $game,
            ));
            if (!$gameLink)
                throw new \Exception('Cant create  buff if there is no gamelink');
            $buff = new \EIP\HRBundle\Entity\HRBuff($item->getSchema()->getBuffSchema(), $gameLink);
            $em->persist($buff);
            // flash message
            $contentString = $this->get('translator')->trans("buff.new") . " : " . $this->get('translator')->trans($buff->getSchema()->getName());
            $notification = new \EIP\HRBundle\Entity\HRNotification(\EIP\HRBundle\Entity\HRNotification::SUCCESS);
            $notification->setContent($contentString);
            $notification->setGame($game);
            $notification->setUser($user);
            $em->persist($notification);
            $this->get('session')->getFlashBag()->add('buff', $contentString);
        }
        elseif ($item->givesResources()) {
            $resourcesTypes = array('water', 'pureWater', 'steel', 'fuel');
            $resourceName = $item->getSchema()->getResourceName();
            if (!in_array($resourceName, $resourcesTypes))
                throw new \Exception("invalid resource name: " . $resourceName);
            $user = $this->getUser();
            $gameLink = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array('game' => $game, 'user' => $user));
            $resources = $gameLink->getResources();
            $resources[$resourceName] += $item->getSchema()->getValue();
            if ($resources[$resourceName] > $resources[$resourceName . 'Stock'])
                $resources[$resourceName] = $resources[$resourceName . 'Stock'];
            $gameLink->setResources($resources);
            // flash message
            $contentString = $this->get('translator')->trans("resources.new") . ' : ' . $item->getSchema()->getValue() . ' ' . $this->get('translator')->trans('resource.' . $resourceName);
            $notification = new \EIP\HRBundle\Entity\HRNotification(\EIP\HRBundle\Entity\HRNotification::SUCCESS);
            $notification->setContent($contentString);
            $notification->setGame($game); // meme chose -- merge, mais pourquoi
            $notification->setUser($user);
            $em->persist($notification);
            $this->get('session')->getFlashBag()->add('resource', $contentString);
        }
        elseif ($item->givesUnits()) { // free units
            // page displaying the type and number of unit to be given, player must select a town to host these new units
            // flash message
            return $this->redirect($this->generateUrl('hr_summon_units', array('itemid' => $item->getId())));
        }
        else
            throw new \Exception("Item with no effect ...");

        // delete the item
        $em->remove($item);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_hero'));
    }

    /**
     * \brief add units in the garrison of a town
     * @return View
     */
    public function summonUnitAction($itemid) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $this->getGame($em);
        $hero = $this->getDoctrine()->getRepository('EIPHRBundle:HRHero')->findOneBy(array(
            'user' => $user,
            'game' => $game,
        ));
        $item = $this->getDoctrine()->getRepository('EIPHRBundle:HRItem')->getItemInfos($itemid, $hero);
        if (!$item->givesUnits())
            throw new \Exception("This item does not summon units");
        if ($this->getRequest()->getMethod() == "POST") {

            $townid = $this->getRequest()->request->get('townid');
            if (!$this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->belongsTo($townid, $user))
                throw new \Exception('This town does not belong to you');
            $garrison = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->getTownGarrison($townid);
            foreach (range(1, $item->getSchema()->getValue()) as $i) {
                $unit = new \EIP\HRBundle\Entity\HRUnit();
                $unit->setArmy($garrison);
                $unit->setSchema($item->getSchema()->getUnitSchema());
                $em->persist($unit);
            }
            // flash message & notification
            $tr = $this->get('translator');
            $contentString = $item->getSchema()->getValue() . ' ' . $tr->trans($item->getSchema()->getUnitSchema()->getName(), array(), 'units') . ' '
                    . $tr->trans('units.new') . ' ' . $garrison->getTown()->getName()
                    . ' [' . $garrison->getTown()->getXCoord() . ', ' . $garrison->getTown()->getYCoord() . ']';
            $notification = new \EIP\HRBundle\Entity\HRNotification(\EIP\HRBundle\Entity\HRNotification::SUCCESS);
            $notification->setContent($contentString);
            $notification->setGame($em->merge($game));
            $notification->setUser($user);
            $em->persist($notification);
            $this->get('session')->getFlashBag()->add('unit', $contentString);
            // delete the item
            $em->remove($item);
            $em->flush();
            return $this->redirect($this->generateUrl('hr_hero'));
        }
        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getAllyTowns($user, $game);
        return $this->render('EIPHRBundle:Game:summonUnit.html.twig', array(
                    'item' => $item,
                    'towns' => $towns
        ));
    }

    public function itemDetailsAction($id) {
        $tr = $this->get('translator');
        $itemSchema = $this->getDoctrine()->getManager()->find('EIPHRBundle:HRItemSchema', $id);
        return new \Symfony\Component\HttpFoundation\JsonResponse(array(
            'name' => $tr->trans($itemSchema->getName(), array(), 'items'),
            'desc' => $tr->trans($itemSchema->getDescription(), array(), 'items'),
            'img' => $itemSchema->getImage()
        ));
    }

    //////
    // Map
    //////

    /**
     * mapAction
     * \brief display the map of the current game
     */
    public function mapAction() {
        $user = $this->getUser();
        $game = $this->getGame();
        $resultArray = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->getTownsAndArmiesForGame($user->getId(), $game->getId());

        $usersID = array();
        foreach ($resultArray as $town) {
            if (!in_array($town['ownerid'], $usersID))
                $usersID[] = $town['ownerid'];
        }
        $usersAndClans = $this->getDoctrine()->getRepository('EIPHRBundle:HRClan')->getClansForUsers($usersID, $game->getId());
        foreach ($resultArray as &$town)
        {
            if (array_key_exists($town['ownerid'], $usersAndClans))
                $town['clan'] = $usersAndClans[$town['ownerid']];
            else
                $town['clan'] = null;
        }

        $unitSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->getAllTranslatedNameAndImage($this->get('translator'));
        return $this->render('EIPHRBundle:Game:map.html.twig', array(
                    'unitSchemas' => json_encode($unitSchemas),
                    'towns' => json_encode($resultArray)
        ));
    }

    /**
    * \brief returns informations about enemy players for the map page
    */
    public function mapPlayersInfoAction()
    {
        $idArray = json_decode($this->getRequest()->query->get('idArray'));
        $infos = $this->getDoctrine()->getRepository('EIPHRBundle:HRHero')->getMapInfoFor($idArray, $this->getGame()->getId());
        // translate hero names
        $tr = $this->get('translator');
        foreach ($infos as &$info)
            $info['heroName'] = $tr->trans($info['heroName'], array(), 'heroes');
        return new  \Symfony\Component\HttpFoundation\JsonResponse($infos);
    }

    //////
    // Army movement
    /////

    /**
     * moveArmyAction
     * \brief move the selected army from his position to another one
     */
    public function moveArmyAction($armyid, $toX, $toY) {
        $user = $this->getUser();
        $game = $this->getGame();
        // check if there is a town at the given coordinates
        $targetTown = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findOneBy(array(
            'xCoord' => $toX,
            'yCoord' => $toY,
            'game' => $game
        ));
        if (!$targetTown) {
            throw new \Exception("No town at given coordinates");
        }
        $army = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->findOneBy(array(
            'user' => $user,
            'game' => $game,
            'id' => $armyid
        ));
        if ($army->getMoving()) {
            throw new \Exception("Army is already moving");
        }
        // get town in same request ?
        $fromX = $army->getTown()->getXCoord();
        $fromY = $army->getTown()->getYCoord();
        if ($fromX == $toX && $fromY == $toY) {
            throw new \Exception("No movement");
        }
        $army->move($toX, $toY);
        $army->setMoving(true);
        $this->getDoctrine()->getManager()->flush();
        if ($this->getRequest()->isXmlHttpRequest())
            return new \Symfony\Component\HttpFoundation\Response("ok");
        return $this->redirect($this->generateUrl('hr_army', array('armyid' => $armyid)));
    }

    /*     * ****************** */
    /*     * ***** CLAN ******* */
    /*     * ****************** */

    /**
     * clanAction
     * \brief display the user's clan
     *
     * @return view
     */
    public function clanAction() {
        $clanRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClan');
        $clanAppRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanApplication');
        $clanMemberRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanMembers');
        $idUser = $this->getUser()->getId();
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $hasClan = $clanMemberRepo->hasClan($idUser, $idGame);
        $clanList = $clanRepo->getAllClan($idGame);

        if ($hasClan) {
            $render = GameController::UserHasClan($clanList, $clanRepo, $this->getUser()->getId());
        } else if ($clanAppRepo->isAlreadyApplicate($idUser, $idGame)) {
            $render = GameController::UserDoesntHaveClanAndApply($clanList);
        } else {
            $form = GameController::createClan();
            $clanList = $clanRepo->getAllClan($idGame);
            if ($form->getData()->getName() != NULL && $form->getData()->getAcronym() != NULL) {
                $render = GameController::UserHasClan($clanList, $clanRepo, $idUser);
            } else {
                $render = GameController::UserDoesntHaveClan($clanList, $form);
            }
        }
        return $render;
    }

    /**
     * createClanAction
     * \brief create a new clan into the database
     */
    public function createClan() {
        $session = $this->getRequest()->getSession();
        $idGame = $session->get('game')->getId();
        $clan = new \EIP\HRBundle\Entity\HRClan();
        $clan->setIdGame($idGame);
        $clan->setPrivatePresentation("");
        $clan->setPublicPresentation("");
        $clan->setRecruitmentStatut(true);
        $clan->setBanner("");

        $form = $this->createForm(new \EIP\HRBundle\Form\HRClanType(), $clan);
        $em = $this->getDoctrine()->getManager();
        $handler = new \EIP\HRBundle\Form\HRClanHandler($form, $this->getRequest(), $em, $this->get('security.encoder_factory'));
        // Check if clan already exist ...
        $em->getConnection()->beginTransaction();
        try {
            if ($handler->process()) {
                $MemberRank = new \EIP\HRBundle\Entity\HRClanRank($clan->getId(), "Admin", true, true, true, true, true, true);
                $em->persist($MemberRank);
                $em->flush();
                $RelMemberClan = new \EIP\HRBundle\Entity\HRClanMembers($clan->getId(), $this->getUser()->getId(), $MemberRank->getId());
                $em->persist($RelMemberClan);
                $em->flush();
            }
            $em->getConnection()->commit();
        } catch (Exception $e) {
            $em->getConnection()->rollback();
            $em->close();
            throw $e;
        }

        return $form;
    }

    /**
     * userHasClanAction
     * \brief get the user's clan and display the view
     */
    public function UserHasClan($clanList, $clanRepo, $userId) {
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $myClan = $clanRepo->getUserClan($userId, $idGame);
        $clanMemberRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanMembers');
        $rankRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanRank');

        $canEdit = $clanMemberRepo->userCanAccessToAdmin($userId, $myClan['id']);
        $member = $clanMemberRepo->getOneLine($this->getUser()->getId(), $idGame);
        $rankName = $rankRepo->getRankName($member[0]->getIdRank());
        $render = $this->render('EIPHRBundle:Game:noClan.html.twig', array(
            'form' => '',
            'myClan' => $myClan,
            'clanList' => $clanList,
            'canEdit' => $canEdit,
            'userID' => $userId,
            'rankname' => $rankName['name'],
        ));

        return $render;
    }

    /**
     * userDoesntHaveClanAndApplyAction
     * \brief display the view
     */
    public function UserDoesntHaveClanAndApply($clanList) {
        $render = $this->render('EIPHRBundle:Game:noClan.html.twig', array(
            'form' => '',
            'myClan' => '',
            'clanList' => $clanList,
        ));
        return $render;
    }

    /**
     * userDoesntHaveClanAndApplyAction
     * \brief display the view
     */
    public function UserDoesntHaveClan($clanList, $form) {
        $render = $this->render('EIPHRBundle:Game:noClan.html.twig', array(
            'form' => $form->createView(),
            'myClan' => '',
            'clanList' => $clanList,
        ));
        return $render;
    }

    /**
     * clanDetailAction
     * \brief display the information of the selected clan
     */
    public function clanDetailAction($clanname) {
        $clanname = htmlspecialchars($clanname);
        $clanRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClan');
        $clanAppRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanApplication');
        $clanMemberRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanMembers');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $idUser = $this->getUser()->getId();
        if (empty($clan_tab)) {
            return $this->render('EIPHRBundle:Game:clanDetail.html.twig', array('clan' => ''));
        }
        $clan = $clan_tab[0];
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        if ($clan->getRecruitmentStatut() && !$clanAppRepo->isAlreadyApplicate($idUser, $idGame) && !$clanMemberRepo->hasClan($idUser, $idGame)) {
            $clanApp = new \EIP\HRBundle\Entity\HRClanApplication();
            $clanApp->setIdClan($clan->getId());
            $clanApp->setIdUser($idUser);
            $clanApp->setPending(0);
            $form = $this->createForm(new \EIP\HRBundle\Form\HRClanApplicationType(), $clanApp);
            $em = $this->getDoctrine()->getManager();
            $handler = new \EIP\HRBundle\Form\HRClanApplicationHandler($form, $this->getRequest(), $em, $this->get('security.encoder_factory'));
            if ($handler->process())
                ;
            $msgPerso = 0;
            $formu = $form->createView();
        } else if (!$clan->getRecruitmentStatut()) {
            $msgPerso = 1;
            $formu = '';
        } else if ($clanAppRepo->isAlreadyApplicate($idUser, $idGame)) {
            $msgPerso = 2;
            $formu = '';
        } else if ($clanMemberRepo->hasClan($idUser, $idGame)) {
            $msgPerso = 3;
            $formu = '';
        }
        $memberList = $clanRepo->getAllMember($clan->getId());
        return $this->render('EIPHRBundle:Game:clanDetail.html.twig', array(
                    'clan' => $clan,
                    'form' => $formu,
                    'msgperso' => $msgPerso,
                    'memberlist' => $memberList,
        ));
    }

    /**
     * clanAdminAction
     * \brief you can administrate your clan and your members here
     */
    public function clanAdminAction($clanname) {
        $em = $this->getDoctrine()->getManager();
        $clanRepo = $em->getRepository('EIPHRBundle:HRClan');
        $clanMemberRepo = $em->getRepository('EIPHRBundle:HRClanMembers');
        $rankRepo = $em->getRepository('EIPHRBundle:HRClanRank');
        $applyRepo = $em->getRepository('EIPHRBundle:HRClanApplication');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $clan = $clan_tab[0];
        $idClan = $clan->getId();
        $listMembers = $clanMemberRepo->getOneLine($this->getUser()->getId(), $idGame);
        $listMember = $listMembers[0];
        $clanRank = new \EIP\HRBundle\Entity\HRClanRank($idClan, "", false, false, false, false, false, false);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRClanRankType(), $clanRank);
        $formEdit = $this->createForm(new \EIP\HRBundle\Form\HRClanType(), $clan);
        $formMember = $this->createForm(new \EIP\HRBundle\Form\HRClanMembersType(), $listMember);

        $rankList = $rankRepo->getAllRankFromClan($idClan);
        $applyList = $applyRepo->getAllApply($idClan);
        $memberList = $clanRepo->getAllMember($idClan);
        $canfiremember = $clanMemberRepo->userCanFireMember($this->getUser()->getId(), $idClan);
        $msgAlert = 0;

        return $this->render('EIPHRBundle:Game:clanAdmin.html.twig', array(
                    'form' => $form->createView(),
                    'rankList' => $rankList,
                    'applyList' => $applyList,
                    'memberList' => $listMembers,
                    'msgAlert' => $msgAlert,
                    'memberlist' => $memberList,
                    'clanname' => $clanname,
                    'idclan' => $idClan,
                    'iduser' => $this->getUser()->getId(),
                    'canfiremember' => $canfiremember,
                    'canedittext' => $clanMemberRepo->userCanEditText($this->getUser()->getId(), $idClan),
                    'cancreaterank' => $clanMemberRepo->userCanCreateRank($this->getUser()->getId(), $idClan),
                    'canrecruit' => $clanMemberRepo->userCanRecruit($this->getUser()->getId(), $idClan),
                    'candeleteclan' => $clanMemberRepo->userCanDeleteClan($this->getUser()->getId(), $idClan),
                    'formedit' => $formEdit->createView(),
                    'formmember' => $formMember->createView(),
        ));
    }

    /**
     * createRankAction
     *  \brief create new rank for a clan
     */
    public function createRankAction($clanname) {
        $em = $this->getDoctrine()->getManager();

        $rankRepo = $em->getRepository('EIPHRBundle:HRClanRank');
        $clanMemberRepo = $em->getRepository('EIPHRBundle:HRClanMembers');
        $clanRepo = $em->getRepository('EIPHRBundle:HRClan');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $clan = $clan_tab[0];
        $idClan = $clan->getId();

        if ($clanMemberRepo->userCanCreateRank($this->getUser()->getId(), $idClan)) {
            $clanRank = new \EIP\HRBundle\Entity\HRClanRank($idClan, "", false, false, false, false, false, false);
            $form = $this->createForm(new \EIP\HRBundle\Form\HRClanRankType(), $clanRank);


            $handler = new \EIP\HRBundle\Form\HRClanRankHandler($form, $this->getRequest(), $em, $this->get('security.encoder_factory'));
            $returnValue = $handler->process($rankRepo);
            if ($returnValue == 2) {
                $msgAlert = 1;
            } else {
                $msgAlert = 0;
            }
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * editClanAction
     *  \brief edit information for a clan
     */
    public function editClanAction($clanname, $id) {
        $clanMemberRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanMembers');

        if ($clanMemberRepo->userCanEditText($this->getUser()->getId(), $id)) {
            $clan = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClan')->find($id);

            $form = $this->createForm(new \EIP\HRBundle\Form\HRClanType(), $clan);
            $handlerclan = new \EIP\HRBundle\Form\HRClanHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);
            if ($handlerclan->process($clan))
                return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
            else
                return new \Symfony\Component\HttpFoundation\Response("ERROR", 500);
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * refusCandidatureAction
     * \brief cancel the player's submission
     */
    public function refusCandidatureAction($clanname, $idcand) {
        $clanMemberRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanMembers');
        $clanRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClan');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $idClan = $clan_tab[0]->getId();

        if ($clanMemberRepo->userCanRecruit($this->getUser()->getId(), $idClan)) {
            $res = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanApplication')->getOneApply($idcand);
            $res[0]->setPending(2);
            $em = $this->getDoctrine()->getManager();
            $em->persist($res[0]);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * acceptCandidatureAction
     * \brief accept the player's submission
     */
    public function acceptCandidatureAction($clanname, $idcand) {
        $clanMemberRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanMembers');
        $clanRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClan');
        $rankRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanRank');
        $applyRepo = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRClanApplication');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $idClan = $clan_tab[0]->getId();

        if ($clanMemberRepo->userCanRecruit($this->getUser()->getId(), $idClan)) {
            $res = $applyRepo->getOneApply($idcand);
            $res[0]->setPending(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($res[0]);

            $member = new \EIP\HRBundle\Entity\HRClanMembers($idClan, 0, 0);
            $member->setIdClan($idClan);
            $member->setIdRank(0);
            $member->setIdUser($res[0]->getIdUser());
            $em->persist($member);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * fireMemberAction
     * \brief fire member in a clan
     */
    public function fireMemberAction($clanname, $id) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EIPHRBundle:HRClanMembers');
        $clanRepo = $em->getRepository('EIPHRBundle:HRClan');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $idClan = $clan_tab[0]->getId();

        if ($repo->userCanFireMember($this->getUser()->getId(), $idClan) && $id != $this->getUser()->getId()) {
            $memberClan = $repo->getOneLine($id, $idGame);
            $em->remove($memberClan[0]);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * updateRankAction
     * \brief called when a member has a new rank
     */
    public function updateRankAction($clanname) {
        $em = $this->getDoctrine()->getManager();

        $clanMemberRepo = $em->getRepository('EIPHRBundle:HRClanMembers');
        $clanRepo = $em->getRepository('EIPHRBundle:HRClan');
        $clanRankRepo = $em->getRepository('EIPHRBundle:HRClanRank');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $idClan = $clan_tab[0]->getId();
        $idUser = $this->getUser()->getId();

        if ($clanMemberRepo->userCanCreateRank($idUser, $idClan)) {
            $member = new \EIP\HRBundle\Entity\HRClanMembers($idClan, 0, 0);
            $form = $this->createForm(new \EIP\HRBundle\Form\HRClanMembersType(), $member);
            $handler = new \EIP\HRBundle\Form\HRClanMembersHandler($form, $this->getRequest(), $em, $this->get('security.encoder_factory'));
            $member2 = $handler->process($member);
            $memberLine = $clanMemberRepo->getOneLine($member2->getIdUser(), $idGame);

            $memberToUpdate = $memberLine[0];
            $rankname = $clanRankRepo->getRankName($memberToUpdate->getIdRank());
            if ($rankname['name'] != 'Admin') {
                $memberToUpdate->setIdRank($member2->getIdRank());
                $em->persist($memberToUpdate);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * deleteRankAction
     * \brief delete a rank in a clan
     */
    public function deleteRankAction($clanname, $idRank) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EIPHRBundle:HRClanMembers');
        $rankRepo = $em->getRepository('EIPHRBundle:HRClanRank');
        $clanRepo = $em->getRepository('EIPHRBundle:HRClan');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();
        $clan_tab = $clanRepo->getInfoClan($clanname, $idGame);
        $idClan = $clan_tab[0]->getId();
        $name_tab = $rankRepo->getRankName($idRank);
        $name = $name_tab['name'];
        if (($repo->userCanCreateRank($this->getUser()->getId(), $idClan)) && ($name != "Admin")) {
            $rank = $rankRepo->getRankFromId($idRank);
            $membersRank = $repo->getLineWithThisRank($idRank);
            foreach ($membersRank as $memberRank) {
                $memberRank->setIdRank(0);
                $em->persist($memberRank);
            }
            $em->remove($rank[0]);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hr_clan_admin', array('clanname' => $clanname)));
    }

    /**
     * leaveClanAction
     * \brief called when a member wants to leave his clan
     */
    public function leaveClanAction($idUser) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('EIPHRBundle:HRClanMembers');
        $repoRank = $em->getRepository('EIPHRBundle:HRClanRank');
        $idGame = $this->getRequest()->getSession()->get('game')->getId();

        $member = $repo->getOneLine($idUser, $idGame);
        $rankName = $repoRank->getRankName($member[0]->getIdRank());
        if ($repo->hasClan($idUser, $idGame) && $rankName['name'] != "Admin") {
            $clanrepo = $em->getRepository('EIPHRBundle:HRClan');
            $clan = $clanrepo->getUserClan($idUser, $idGame);
            $listMember = $clanrepo->getAllMember($clan['id']);
            if (count($listMember) == 1) {
                GameController::deleteClanAction($clan['id']);
            }
            $linesToDel = $repo->getOneLine($idUser, $idGame);
            foreach ($linesToDel as $lineToDel)
                $em->remove($lineToDel);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('hr_clan'));
    }

    /**
     * deleteClanAction
     * \brief delete a team with rank and member
     */
    public function deleteClanAction($idClan) {
        $em = $this->getDoctrine()->getManager();
        $clanRepo = $em->getRepository('EIPHRBundle:HRClan');
        $clanRankRepo = $em->getRepository('EIPHRBundle:HRClanRank');
        $clanMemberRepo = $em->getRepository('EIPHRBundle:HRClanMembers');

        if ($clanMemberRepo->userCanDeleteClan($this->getUser()->getId(), $idClan)) {
            $memberList = $clanRepo->getMembersLine($idClan);
            $rankList = $clanRankRepo->getRankLines($idClan);
            $clan = $clanRepo->getUserClanById($idClan);
            $em->getConnection()->beginTransaction();
            try {
                foreach ($memberList as $member)
                    $em->remove($member);
                foreach ($rankList as $rank)
                    $em->remove($rank);
                $em->remove($clan);
                $em->flush();
                $em->getConnection()->commit();
            } catch (Exception $e) {
                $em->getConnection()->rollback();
                $em->close();
                throw $e;
            }
        }
        return $this->redirect($this->generateUrl('hr_clan'));
    }

    /*     * ****************** */
    /*     * ** END OF CLAN *** */
    /*     * ****************** */



    /*     * ****************** */
    /*     * **** POP UP ****** */
    /*     * ****************** */

    /**
     * popupChatAction
     * \brief open a popup and display the chat into
     *
     * @return view
     */
    public function popupChatAction() {
        // username - game - clan - generated chat token
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $this->getGame($em);
        if (!$game)
            throw new \Exception();
        $chatToken = new \EIP\HRBundle\Entity\ChatToken($user, $game);
        $em->persist($chatToken);
        $em->flush();
        return parent::render('EIPHRBundle:Game:popupChat.html.twig', array(
                    'tokenValue' => $chatToken->getValue(),
        ));
    }

    /**
     * validateCredentialsAction
     * \brief check if the identity of the user
     *
     * @return Response
     */
    public function validateCredentialsAction() {
        try {
            $token = $this->getRequest()->request->get('token');
            if (!$token)
                throw new \Exception("No token received");
            $chatToken = $this->getDoctrine()->getRepository('EIPHRBundle:ChatToken')->getToken($token);
            if (!$chatToken)
                throw new \Exception("No matching token in database");
            $user = $chatToken->getUser();
            $username = $user->getUsername();
            $game = $chatToken->getGame();
            $gamename = $game->getName();
            $gameid = $game->getId();
            $clanname = $this->getDoctrine()->getRepository('EIPHRBundle:HRClan')->getUserClanByGame($user, $game);
            if ($clanname == null)
                $clanname = "NOCLAN";
            else
                $clanname = $clanname->getName();
            $em = $this->getDoctrine()->getManager();
            $em->remove($chatToken);
            $em->flush();
            $locale = $this->getRequest()->getLocale();
            return new \Symfony\Component\HttpFoundation\Response('OK ' . $username . ' ' . $clanname . ' ' . $gameid . ' ' . $gamename . ' ' . $locale);
        } catch (\Exception $ex) {
            return new \Symfony\Component\HttpFoundation\Response('KO');
        }
    }

    /*     * ****************** */
    /*     * ** END POP UP **** */
    /*     * ****************** */

    /**
     * \brief update the user preferences ( show queues on / off )
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updatePreferencesAction() {
        $openQueue = $this->getRequest()->request->get('openQueue');
        if ($openQueue !== null) {
            $this->getRequest()->getSession()->set('openQueue', $openQueue);
            return new \Symfony\Component\HttpFoundation\Response($openQueue);
        }
        return new \Symfony\Component\HttpFoundation\Response(200);
    }

    /**
     * \brief display the buffs currently active in this game
     * @return View
     */
    public function currentBuffsAction() {
        $user = $this->getUser();
        $game = $this->getGame();
        $buffs = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuff')->getCurrentBuffs($user, $game);
        $sortedBuffs = \EIP\HRBundle\Entity\HRBuff::sortByType($buffs);
        // calculate the total value for all buffs present
        $totals = array();
        $keys = array('health', 'attack', 'armor', 'speed', 'resources', 'buildingTime', 'trainingTime');
        foreach ($keys as $key) {
            $totals[$key] = 0.0;
            foreach ($sortedBuffs[$key] as $b) {
                $totals[$key] += $b->getSchema()->getValue();
            }
        }
        // get the hero
        $hero = $this->getDoctrine()->getRepository('EIPHRBundle:HRHero')->getHeroInformations($user, $game);
        return $this->render('EIPHRBundle:Game:currentBuffs.html.twig', array(
                    'sortedBuffs' => $sortedBuffs,
                    'totals' => $totals,
                    'hero' => $hero
        ));
    }

    /**
     * \brief return a page containing 40 notifications, pagination available
     * @param integer $page
     * @return View
     */
    public function notificationsAction($page) {
        $user = $this->getUser();
        $game = $this->getGame();
        $notifications = $this->getDoctrine()->getRepository('EIPHRBundle:HRNotification')
                ->getNotifications($user, $game, $page);
        $notificationsCount = $this->getDoctrine()->getRepository('EIPHRBundle:HRNotification')
                ->getNotificationsCount($user, $game);
        // cas '0' a gere pour twig
        if ($notificationsCount == 0)
            $notificationsCount = 1;

        return $this->render('EIPHRBundle:Game:notifications.html.twig', array(
                    'notifications' => $notifications,
                    'maxPage' => ceil($notificationsCount / 40.0),
                    'currentPage' => $page
        ));
    }

    /**
     * \brief return a page containing 40 battlereports, pagination available
     * @param integer $page
     * @return View
     */
    public function battleReportsAction($page) {
        $user = $this->getUser();
        $game = $this->getGame();
        $battleReports = $this->getDoctrine()->getRepository('EIPHRBundle:HRBattleReport')
                ->getBattleReports($user, $game, $page);
        $battleReportsCount = $this->getDoctrine()->getRepository('EIPHRBundle:HRBattleReport')
                ->getBattleReportsCount($user, $game);
        // cas '0' a gere pour twig
        if ($battleReportsCount == 0)
            $battleReportsCount = 1;

        return $this->render('EIPHRBundle:Game:battleReports.html.twig', array(
                    'battleReports' => $battleReports,
                    'maxPage' => ceil($battleReportsCount / 40.0),
                    'currentPage' => $page
        ));
    }

    /**
     * \brief display the information contained in a battle report
     * @param integer $id
     * @return View
     */
    public function battleReportAction($id) {
        $report = $this->getDoctrine()->getRepository('EIPHRBundle:HRBattleReport')->getReportFullInfos($id);
        $unitSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->findAll();
        $schemaArray = array();
        foreach ($unitSchemas as $s)
            $schemaArray[$s->getId()] = $s;
        return $this->render('EIPHRBundle:Game:battleReport.html.twig', array(
                    'report' => $report,
                    'schemaArray' => $schemaArray
        ));
    }

    /**
     * \brief Quests management page
     * @return View
     */
    public function questsAction() {
        $user = $this->getUser();
        $game = $this->getGame();
        $currentQuests = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuest')->getCurrentQuests($user, $game);
        $completedQuests = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuest')->getCompletedQuests($user, $game);
        $availableQuests = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuestGameLink')->getAvailableQuests($game, $currentQuests, $completedQuests);

        return $this->render('EIPHRBundle:Game:quests.html.twig', array(
                    'currentQuests' => $currentQuests,
                    'availableQuests' => $availableQuests,
                    'completedQuests' => $completedQuests
        ));
    }

    /**
     * \brief Displays the details of a quest
     * @param integer $schemaid
     * @return View
     */
    public function questAction($schemaid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuestSchema')->find($schemaid);
        $quest = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuest')
                ->getQuestBySchemaID($this->getUser(), $this->getgame(), $schemaid);
        $additionalInformation = null;
        // for a build type quest, pass an array containing the rvalues of the schemas
        if ($schema->getType() == \EIP\HRBundle\Entity\HRQuestSchema::BUILD) {
            $result = $this->getDoctrine()->getManager()
                            ->createQuery("SELECT b.id id, b.rValue rvalue, b.name name FROM EIPHRBundle:HRBuildingSchema b")->getArrayResult();
            $additionalInformation = array();
            foreach ($result as $val)
                $additionalInformation[$val['id']] = array('rvalue' => $val['rvalue'], 'name' => $val['name']);
        } elseif ($schema->getType() == \EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT) {
            $result = $this->getDoctrine()->getManager()
                            ->createQuery("SELECT u.id id, u.img img, u.name name FROM EIPHRBundle:HRUnitSchema u")->getArrayResult();
            $additionalInformation = array();
            foreach ($result as $val)
                $additionalInformation[$val['id']] = array('img' => $val['img'], 'name' => $val['name']);
        }

        return $this->render('EIPHRBundle:Game:quest.html.twig', array(
                    'schema' => $schema,
                    'hasQuest' => $quest != null,
                    'quest' => $quest,
                    'additionalInformation' => $additionalInformation
        ));
    }

    /**
     * \brief join an available quest
     * @param integer $schemaid
     * @return View
     * @throws \Exception
     */
    public function joinQuestAction($schemaid) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $this->getGame($em);
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuestSchema')->find($schemaid);
        $availabe = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuestGameLink')->isQuestAvailable($schema, $game, $user);
        if (!$availabe)
            throw new \Exception("This quest is not available for you to join");
        $gamelink = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
            'user' => $user->getId(),
            'game' => $game->getId()
        ));

        $q = new \EIP\HRBundle\Entity\HRQuest();
        $q->setSchema($schema);
        $q->setGame($game);
        $q->setUser($user);
        $q->setGameLink($gamelink);
        $em->persist($q);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_quests'));
    }

    /**
     * \brief give resources to complete a quest
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function giveResourcesAction() {
        try {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();
            $game = $this->getGame($em);
            $gamelink = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->findOneBy(array(
                'user' => $user,
                'game' => $game
            ));
            // 1. check if the user has the quest and if the quest is a GIVE_RESOURCES type quest
            $schemaid = $this->getRequest()->request->get('schemaid');
            $q = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuest')->getQuestBySchemaID($user, $game, $schemaid);
            if (!$q)
                throw new \EIP\HRBundle\Utils\HRUserException("You cannot complete this quest");
            if ($q->getState() != \EIP\HRBundle\Entity\HRQuest::STATE_ONGOING)
                throw new \EIP\HRBundle\Utils\HRUserException("This cannot be completed anymore");
            if ($q->getSchema()->getType() != \EIP\HRBundle\Entity\HRQuestSchema::GIVE_RESOURCES)
                throw new \EIP\HRBundle\Utils\HRUserException("Invalid quest type");
            // 2. transfer the resources and update the entities
            $givenResources = $q->getData();
            $requiredResources = $q->getSchema()->getData();
            foreach (array('water', 'pureWater', 'steel', 'fuel') as $resourceKey) {
                $r = floor($this->getRequest()->request->get($resourceKey));
                $r = $r < 0 ? 0 : $r;
                if (($givenResources[$resourceKey] + $r) > $requiredResources[$resourceKey])
                    $r = $requiredResources[$resourceKey] - $givenResources[$resourceKey];
                $givenResources[$resourceKey] += $r;
                $gamelink->removeResource($resourceKey, $r);
            }
            $q->setData($givenResources);
            // 3. check completion, update quest entity, give the rewards & flush the changes
            if ($q->isCompleted()) {
                \EIP\HRBundle\Utils\HRTools::completeQuest($q, $user, $game, $this->get('session')->getFlashBag(), $this->get('translator'), $em);
            }
            $em->flush();
            return new \Symfony\Component\HttpFoundation\Response("ok");
        } catch (\EIP\HRBundle\Utils\HRUserException $e) {
            return new \Symfony\Component\HttpFoundation\Response($e->getMessage(), 406);
        } catch (\Exception $e) {
            return new \Symfony\Component\HttpFoundation\Response('Unknown error', 500);
        }
    }

    /**
     * \brief Displays the profile of a player
     * @param string $username
     * @return View
     */
    public function profileAction($username) {
        $loggedIn = ($this->get('session')->get('game') != null);
        $user = $this->getDoctrine()->getRepository('EIPHRBundle:HRUser')->getUserProfile($username);
        $nbGames = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->getGamesNumber($user->getId());
        return $this->render('EIPHRBundle:Game:profile.html.twig', array(
                    'nbGames' => $nbGames,
                    'profile' => $user,
                    'loggedIn' => $loggedIn
        ));
    }

    /**
     * \brief Display the list of all the available achievements
     * @return View
     */
    public function achievementsAction() {
        $schemas = $this->getDoctrine()->getManager()->getRepository('EIPHRBundle:HRAchievementSchema')->findAll();
        $loggedIn = ($this->get('session')->get('game') != null);
        return $this->render('EIPHRBundle:Game:achievements.html.twig', array(
                    'schemas' => $schemas,
                    'loggedIn' => $loggedIn
        ));
    }

    /**
    * \brief Join the test game or create it if it does not exist yet
    * @return View
    */
    public function testGameAction() {
        $user = $this->getUser();
        $testGame = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->getTestGameForUser($user);
        if (!$testGame)
        {
            $testGame = \EIP\HRBundle\Utils\TestGame::createTestGame($user, $this->getDoctrine()->getEntityManager());
        }
        // set the game as current game
        $this->getDoctrine()->getEntityManager()->detach($testGame);
        $this->get('session')->set('game', $testGame);

        $translatedMessage = $this->get('translator')->trans('this.is.a.testgame');
        $this->get('session')->getFlashBag()->add('info', $translatedMessage);
        return $this->redirect($this->generateUrl('hr_game_dashboard'));
    }

    /**
    * \brief renders the game result page
    * @return View
    */
    public function gameResultAction($gameid)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $game = $em->getRepository('EIPHRBundle:HRGame')->find($gameid);
        $score = $em->getRepository('EIPHRBundle:HRScore')->findOneBy(array(
            'game' => $game->getId()
        ));

        $userClan =  $em->getRepository('EIPHRBundle:HRClan')->getUserClanByGame($user, $game);

        $victory = ($score->getWinnerUser() != null && $score->getWinnerUser()->getId() == $user->getId())
                    || ($userClan != null && $score->getWinnerClan() != null && $score->getWinnerClan()->getId() == $userClan->getId());

        return $this->render('EIPHRBundle:Game:gameResult.html.twig', array(
            'victory' => $victory,
            'score' => $score,
            'game' => $game
            ));
    }

    /**
     * \brief for test purposes
     * @return ~
     */
    public function testAction() {
        $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->updateOpenedGamesList();
        return $this->redirect($this->generateUrl('hr_games'));
    }
    public function test2Action() {
        // return $this->render('EIPHRBundle:Test:test2.html.twig',array(
        //     'schemaViews' => json_encode($schemaViews, JSON_PRETTY_PRINT),
        //     'trainingTimeReduction' => $trainingTimeReduction,
        //     'unitInfos' => json_encode($unitInfos, JSON_PRETTY_PRINT),
        //     'buildingInfos' => json_encode($buildingInfos, JSON_PRETTY_PRINT),
        //     'technologyInfos' => json_encode($technologyInfos, JSON_PRETTY_PRINT),
        //     'town' => $town
        // ));
    }
    public function test3Action() {
        // return $this->render('EIPHRBundle:Test:test3.html.twig');
    }

    ////// Utils //////

    /**
    * \brief Changes the locale of the user
    */
    public function changeLocaleAction($locale)
    {
        $newLocale = ($locale == 'fr') ? 'fr' : 'en';
        $this->getDoctrine()->getRepository('EIPHRBundle:HRUser')->updateLocaleForUser($this->getUser(), $newLocale);
        return new \Symfony\Component\HttpFoundation\Response(200);
    }

    /**
     * \brief return the current game stored in the session, merge with the EntityManager if given
     * @param EntityManager $em
     * @return HRGame
     */
    public function getGame($em = null)
    {
        $game = $this->getRequest()->getSession()->get('game');
        if ($em && !$em->contains($game))
            return $em->merge($game);
        return $game;
    }


    /**
    *   \brief returns the list of all the usernames
    */
    public function getUsernamesAction()
    {
        $usernames = $this->getDoctrine()->getRepository('EIPHRBundle:HRUser')->getUsernames();
        return new  \Symfony\Component\HttpFoundation\JsonResponse($usernames);
    }


}

