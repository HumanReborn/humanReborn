<?php

namespace EIP\HRBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use EIP\HRBundle\Entity\HRUser;
use EIP\HRBundle\Entity\HRTechnologySchema;
use EIP\HRBundle\Entity\HRGame;
use EIP\HRBundle\Entity\HRClan;
use EIP\HRBundle\Entity\HRMessage;
use EIP\HRBundle\Entity\HRGameRepository;
use EIP\HRBundle\Entity\HRMessageRepository;
use EIP\HRBundle\Form\HRGameType;
use EIP\HRBundle\Form\HRGameHandler;
use EIP\HRBundle\Form\HRUserAdmType;
use EIP\HRBundle\Form\HRUserAdmHandler;
use EIP\HRBundle\Form\HRClanAdmType;
use EIP\HRBundle\Form\HRClanAdmHandler;
use EIP\HRBundle\Form\HRMessageType;
use EIP\HRBundle\Form\HRMessageHandler;

/**
  \class AdminController
  \brief Controller for the administrator module of HumanReborn
 */
class AdminController extends Controller {

    /**
      \fn View loginAction(void)
      \brief administration login page. Login logic
      @return View $view
     */
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
            $this->redirect($this->generateUrl('hr_world_selection'));
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render('EIPHRBundle:Admin:login.html.twig', array(
                    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                    'error' => $error,
        ));
    }

    /**
      \fn View dashboardAction(void)
      \brief  administration homepage
      @return View $view
     */
    public function dashboardAction() {
        return $this->render('EIPHRBundle:Admin:dashboard.html.twig');
    }

    /**
      \fn View usersAction(void)
      \brief  retrieve the different users and pass them to the view
      @return View $view
     */
    public function usersAction() {
        $users = $this->getDoctrine()->getRepository("EIPHRBundle:HRUser")->findAll();
        return $this->render('EIPHRBundle:Admin:users.html.twig', array(
                    'users' => $users,
        ));
    }

    /**
      \fn View userEditAction(int)
      \brief read or write (GET | POST) informations about a user based on the passed ID.
      @param int $userID
      @return View $view
     */
    public function userEditAction($id) {
        $user = $this->getDoctrine()->getRepository('EIPHRBundle:HRUser')->find($id);
        $form = $this->createForm(new HRUserAdmType(), $user);
        $handler = new HRUserAdmHandler($form, $this->getRequest(), $this->getDoctrine()->getManager());
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_users'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRUser',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View userGameListAction(int)
      \brief Fetch the games of a certain user based on its ID and pass them to the view
      @param int $userID
      @return View $view
     */
    public function userGameListAction($userid) {
        $gameLinks = $this->getDoctrine()->getRepository('EIPHRBundle:HRGameLink')->getGamesByUserId($userid);
        return $this->render('EIPHRBundle:Admin:_userGameList.html.twig', array(
                    'userid' => $userid,
                    'gameLinks' => $gameLinks,
        ));
    }

    /**
      \fn View userGameDetailAction(int, int)
      \brief Fetch the details of a game for a user
      @param int $userID
      @param int $gameID
      @return View $view
     */
    public function userGameDetailAction($userid, $gameid) {
        // one request -- class UserGameInfo
        $user = $this->getDoctrine()->getRepository('EIPHRBundle:HRUser')->find($userid);
        $game = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($gameid);
        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->findBy(array('owner' => $userid, 'game' => $gameid));
        $technologies = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnology')->findBy(array('user' => $userid, 'game' => $gameid));
        //$armies = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->findBy(array('user' => $userid, 'game' => $gameid));
        $armies = null;
        return $this->render('EIPHRBundle:Admin:userGameDetail.html.twig', array(
                    'user' => $user,
                    'game' => $game,
                    'towns' => $towns,
                    'technologies' => $technologies,
                    'armies' => $armies,
        ));
    }

    /*
     * GAMES
     */

    /**
      \fn View gamesAction()
      \brief Fetch all games
      @return View $view
     */
    public function gamesAction() {
        $games = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->findAll();
        return $this->render('EIPHRBundle:Admin:games.html.twig', array(
                    'games' => $games,
        ));
    }

    /**
      \fn View gameEditAction(int)
      \brief Read or write (GET | POST) informations about a game based on its ID
      @return View $view
     */
    public function gameEditAction($id) {
        $game = $this->getDoctrine()->getRepository('EIPHRBundle:HRGame')->find($id);
        $form = $this->createForm(new HRGameType, $game);
        $handler = new HRGameHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_games'));
        }
        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRGame',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View gameAddAction()
      \brief Get a form to add a game or add the submitted game ( GET | POST )
      @return View $view
     */
    public function gameAddAction() {
        $game = new HRGame();
        $form = $this->createForm(new HRGameType, $game);
        $handler = new HRGameHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_games'));
        }
        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRGame',
                    'form' => $form->createView(),
        ));
    }

    /*
     *  ENDGAMES
     */


    /*
     *  CLANS
     */

    /**
      \fn View clansAction()
      \brief Retrieve the list of the clans
      @return View $view
     */
    public function clansAction() {
        $clans = $this->getDoctrine()->getRepository('EIPHRBundle:HRClan')->getClansList();
        return $this->render('EIPHRBundle:Admin:clans.html.twig', array(
                    'clans' => $clans,
        ));
    }

    /**
      \fn View clanEditAction(int)
      \brief Get a form to edit a clan or update the submitted clan ( GET | POST )
      @param int $clanID
      @return View $view
     */
    public function clanEditAction($id) {
        $clan = $this->getDoctrine()->getRepository('EIPHRBundle:HRClan')->find($id);
        $form = $this->createForm(new HRClanAdmType, $clan);
        $handler = new HRClanAdmHandler($form, $this->getRequest(), $this->getDoctrine()->getManager());
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_clans'));
        }
        return $this->render('EIPHRBundle:Admin:clanEdit.html.twig', array(
                    'form' => $form->createView(),
                    'clan' => $clan,
        ));
    }

    /**
      \fn View clanMemberListAction(int)
      \brief Get the list of HRUsers for a clan based on its ID
      @param int $clanID
      @return View $view
     */
    public function clanMemberListAction($id) {
        throw new \Exception("To be redone - HRClanLink -> HRClanMembers");
        $clan = $this->getDoctrine()->getRepository('EIPHRBundle:HRClan')->find($id);
        $users = $this->getDoctrine()->getRepository('EIPHRBundle:')->getClanMembers($id);
        return $this->render('EIPHRBundle:Admin:clanMemberList.html.twig', array(
                    'clan' => $clan,
                    'users' => $users,
        ));
    }

    /**
      \fn View clanEditAction()
      \brief Get a form to add a clan or add the submitted clan ( GET | POST )
      @return View $view
     */
    public function addClanAction() {
        $clan = new HRClan();
        $form = $this->createForm(new HRClanAdmType(), $clan);
        $handler = new HRClanAdmHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_clans'));
        }
        return $this->render('EIPHRBundle:Admin:addClan.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /*
     *  ENDCLANS
     */
    // messages

    /**
      \fn View addMessageAction()
      \brief Get a form to add a HRMessage or add the submitted HRMessage ( GET | POST )
      @return View $view
     */
    public function addMessageAction() {
        $message = new HRMessage();
        $form = $this->createForm(new HRMessageAdmType(true), $message, array(
            'em' => $this->getDoctrine()->getManager()
        ));
        $handler = new HRMessageHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_messages'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRMessage',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View messagesAction()
      \brief Get a list off all the HRMessages
      @return View $view
     */
    public function messagesAction() {
        $messages = $this->getDoctrine()->getRepository('EIPHRBundle:HRMessage')->fetchAll();
        return $this->render('EIPHRBundle:Admin:messages.html.twig', array(
                    'messages' => $messages,
        ));
    }

    /**
      \fn View editMessageAction(int)
      \brief Get a form to edit a HRMessage or update the submitted HRMessage ( GET | POST )
      @param int $messageID
      @return View $view
     */
    public function editMessageAction($id) {
        $message = $this->getDoctrine()->getRepository('EIPHRBundle:HRMessage')->find($id);

        $form = $this->createForm(new HRMessageType(true), $message, array(
            'em' => $this->getDoctrine()->getManager(),
        ));
        $handler = new HRMessageHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_messages'));
        }
        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRMessage',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View deleteMessageAction(int)
      \brief Delete a message based on its ID
      @param int $messageID
      @return View $view
     */
    public function deleteMessageAction($id) {
        $entity = $this->getDoctrine()->getRepository('EIPHRBundle:HRMessage')->find($id);
        $this->getDoctrine()->getManager()->remove($entity);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('hr_adm_messages'));
    }

    //
    // technologies
    /**
      \fn View technologiesAction()
      \brief Get a list of all the technologies
      @return View $view
     */
    public function technologiesAction() {
        $technologies = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->findAll();
        return $this->render('EIPHRBundle:Admin:technologies.html.twig', array(
                    'technologies' => $technologies
        ));
    }

    /**
      \fn View addTechnologyAction()
      \brief Get a form to add a HRTechnologySchema or insert the submitted HRTechnologySchema ( GET | POST )
      @return View $view
     */
    public function addTechnologyAction() {
        $techno = new HRTechnologySchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRTechnologySchemaType(), $techno);
        $handler = new \EIP\HRBundle\Form\HRTechnologySchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);
        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_technologies'));

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRTechnologySchema',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View editTechnologyAction(int)
      \brief Get a form to edit a HRTechnologySchema or update the submitted HRTechnologySchema ( GET | POST )
      @param int $technoID
      @return View $view
     */
    public function editTechnologyAction($technoid) {
        $techno = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->find($technoid);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRTechnologySchemaType(), $techno);
        $handler = new \EIP\HRBundle\Form\HRTechnologySchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_technologies'));
        }
        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRTechnologySchema',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View deleteTechnologyAction(int)
      \brief Delete a HRTechnologySchema based on its ID
      @param int $technoID
      @return View $view
     */
    public function deleteTechnologyAction($technoid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRTechnologySchema')->find($technoid);
        $this->getDoctrine()->getManager()->remove($schema);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('hr_adm_technologies'));
    }

    //
    // buildings
    /**
      \fn View buildingsAction()
      \brief Get a list of all the HRBuildingSchema
      @return View $view
     */
    public function buildingsAction() {
        $buildings = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->findAll();
        return $this->render('EIPHRBundle:Admin:buildings.html.twig', array(
                    'buildings' => $buildings,
        ));
    }

    /**
      \fn View addBuildingAction()
      \brief Get a form to add a HRBuildingSchema or insert the submitted HRBuildingSchema ( GET | POST )
      @return View $view
     */
    public function addBuildingAction() {
        $bschema = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRBuildingSchemaAdmType(), $bschema);
        $handler = new \EIP\HRBundle\Form\HRBuildingSchemaAdmHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);
        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_buildings'));


        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRBuildingSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View editBuildingAction(int)
      \brief Get a form to edit a HRBuildingSchema or update the submitted HRBuildingSchema ( GET | POST )
      @param int $schemaID
      @return View $view
     */
    public function editBuildingAction($id) {
        $bschema = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->find($id);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRBuildingSchemaAdmType(), $bschema);
        $handler = new \EIP\HRBundle\Form\HRBuildingSchemaAdmHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);
        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_buildings'));

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRBuildingSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View deleteBuildingAction(int)
      \brief Delete a HRBuildingSchema using the given ID
      @param int $schemaID
      @return View $view
     */
    public function deleteBuildingAction($id) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->find($id);
        $this->getDoctrine()->getManager()->remove($schema);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('hr_adm_buildings'));
    }

    // endbuildings

    /**
      \fn View townsAction()
      \brief Get a list of all the HRTown
      @return View $view
     */
    public function townsAction() {
        $towns = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->fetchTowns();
        return $this->render('EIPHRBundle:Admin:towns.html.twig', array(
                    'towns' => $towns,
        ));
    }

    /**
      \fn View addTownAction()
      \brief Get a form to add a HRTown or insert the submitted HRTown ( GET | POST )
      @return View $view
     */
    public function addTownAction() {
        $town = new \EIP\HRBundle\Entity\HRTown();

        $form = $this->createForm(new \EIP\HRBundle\Form\HRTownType(), $town, array(
            'em' => $this->getDoctrine()->getManager(), // passing em for the dataTransformers -- see: Symfony2 datatransformers @ google
        ));
        $handler = new \EIP\HRBundle\Form\HRTownHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);
        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_towns'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRTown',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View editTownAction(int)
      \brief Get a form to edit a HRTown or update the submitted HRTown ( GET | POST )
      @param int $townID
      @return View $view
     */
    public function editTownAction($id) {
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->find($id);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRTownType, $town, array(
            'em' => $this->getDoctrine()->getManager()
        ));
        $handler = new \EIP\HRBundle\Form\HRTownHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);
        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_towns'));

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRTown',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View deleteTownAction(int)
      \brief Delete a HRTown using the given ID
      @param int $townID
      @return View $view
     */
    public function deleteTownAction($townid) {
        $town = $this->getDoctrine()->getRepository('EIPHRBundle:HRTown')->find($townid);
        $this->getDoctrine()->getManager()->remove($town);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('hr_adm_towns'));
    }

    // armies
    /**
      \fn View armiesAction()
      \brief Get a list of all the HRArmy
      @return View $view
     */
    public function armiesAction() {
        $armies = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->fetchAllArmies();
        return $this->render('EIPHRBundle:Admin:armies.html.twig', array(
                    'armies' => $armies,
        ));
    }

    /**
      \fn View addArmyAction()
      \brief Get a form to add a HRArmy or insert the submitted HRArmy ( GET | POST )
      @return View $view
     */
    public function addArmyAction() {
        $army = new \EIP\HRBundle\Entity\HRArmy();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRArmyType(), $army, array(
            'em' => $this->getDoctrine()->getManager(),
        ));

        $handler = new \EIP\HRBundle\Form\HRArmyHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_armies'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRArmy',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View editArmyAction(int)
      \brief Get a form to edit a HRArmy or update the submitted HRArmy ( GET | POST )
      @param int $armyID
      @return View $view
     */
    public function editArmyAction($armyid) {
        $army = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->find($armyid);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRArmyType(), $army, array(
            'em' => $this->getDoctrine()->getManager(),
        ));

        $handler = new \EIP\HRBundle\Form\HRArmyHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_armies'));
        }
        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRArmy',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View deleteArmyAction(int)
      \brief Delete a HRArmy using the passed ID
      @param int $armyID
      @return View $view
     */
    public function deleteArmyAction($armyid) {
        $army = $this->getDoctrine()->getRepository('EIPHRBundle:HRArmy')->find($armyid);
        $this->getDoctrine()->getManager()->remove($army);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('hr_adm_armies'));
    }

    // unit schema
    /**
      \fn View unitsAction()
      \brief Get the list of all the HRUnitSchema
      @return View $view
     */
    public function unitsAction() {
        $units = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->findAll();
        return $this->render('EIPHRBundle:Admin:units.html.twig', array(
                    'units' => $units,
        ));
    }

    /**
      \fn View addUnitAction()
      \brief Get a form to add a HRUnitSchema or insert the submitted HRUnitSchema ( GET | POST )
      @return View $view
     */
    public function addUnitAction() {
        $schema = new \EIP\HRBundle\Entity\HRUnitSchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRUnitSchemaType(), $schema);

        $handler = new \EIP\HRBundle\Form\HRUnitSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_units'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRUnitSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View editUnitAction(int)
      \brief Get a form to edit a HRUnitSchema or update the submitted HRUnitSchema ( GET | POST )
      @param int $schemaID
      @return View $view
     */
    public function editUnitAction($schemaid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->find($schemaid);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRUnitSchemaType(), $schema);

        $handler = new \EIP\HRBundle\Form\HRUnitSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_units'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRUnitSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
      \fn View deleteUnitAction(int)
      \brief Delete a HRUnitSchema using the given ID
      @param int $schemaID
      @return View $view
     */
    public function deleteUnitAction($schemaid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->find($schemaid);
        $this->getDoctrine()->getManager()->remove($schema);
        $this->getDoctrine()->getManager()->flush();
        return $this->redirect($this->generateUrl('hr_adm_units'));
    }

    ///////
    // Hero
    ///////
    /**
     * \brief display the list of the existing hero schemas
     * @return View
     */
    public function heroesAction() {
        $heroes = $this->getDoctrine()->getRepository('EIPHRBundle:HRHeroSchema')->findAll();
        return $this->render('EIPHRBundle:Admin:heroes.html.twig', array(
                    'heroes' => $heroes
        ));
    }

    /**
     * \brief add a hero schema
     * @return View
     */
    public function addHeroAction() {
        $schema = new \EIP\HRBundle\Entity\HRHeroSchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRHeroSchemaType(), $schema);

        $handler = new \EIP\HRBundle\Form\HRHeroSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_heroes'));
        }

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRHeroSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
     * \brief edit a hero schema
     * @return View
     */
    public function editHeroAction($id) {
        $heroSchema = $this->getDoctrine()->getRepository('EIPHRBundle:HRHeroSchema')->find($id);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRHeroSchemaType(), $heroSchema);
        $handler = new \EIP\HRBundle\Form\HRHeroSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);

        if ($handler->process()) {
            return $this->redirect($this->generateUrl('hr_adm_heroes'));
        }
        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRHeroSchema',
                    'form' => $form->createView(),
        ));
    }

    /////////////
    // FORUM
    /////////////

    /**
     * \brief display the list of the existing forum sections
     * @return View
     */
    public function forumAction() {
        $sections = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumSection')->findAll();
        return $this->render('EIPHRBundle:Admin:forum.html.twig', array(
                    'sections' => $sections,
        ));
    }

    /**
     * \brief list the topics in a forum section
     * @return View
     */
    public function forumSectionAction($sectionid) {
        $section = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumSection')->find($sectionid);
        $topics = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')->getTopicsFromSection($sectionid);
        return $this->render("EIPHRBundle:Admin:forumSection.html.twig", array(
                    'section' => $section,
                    'topics' => $topics
        ));
    }

    /**
     * \brief display the content of a topic
     * @return View
     */
    public function forumTopicAction($topicid) {
        $topic = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')->getTopic($topicid);
        return $this->render('EIPHRBundle:Admin:forumTopic.html.twig', array(
                    'topic' => $topic
        ));
    }

    /**
     * \brief delete a topic
     * @return View
     */
    public function forumDeleteTopicAction($topicid) {
        $topic = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')->find($topicid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($topic);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_adm_forum_section', array('sectionid' => $topic->getSection()->getId())));
    }

    /**
     * \brief delete a post
     * @return View
     */
    public function forumDeletePostAction($postid) {
        $post = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumPost')->find($postid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_adm_forum_topic', array('topicid' => $post->getTopic()->getId())));
    }

    /**
     * \brief list all of the item schema
     * @return View
     */
    public function itemsAction() {
        $schemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRItemSchema')->findAll();
        return $this->render('EIPHRBundle:Admin:items.html.twig', array(
                    'schemas' => $schemas
        ));
    }

    /**
     * \brief add a item schema
     * @return View
     */
    public function addItemAction() {
        $itemSchema = new \EIP\HRBundle\Entity\HRItemSchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRItemSchemaType(), $itemSchema);
        $handler = new \EIP\HRBundle\Form\HRItemSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_items'));


        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRItemSchema',
                    'form' => $form->createView()
        ));
    }

    /**
     * \brief edit a item schema
     * @return View
     */
    public function editItemAction($schemaid) {
        $itemSchema = $this->getDoctrine()->getRepository('EIPHRBundle:HRItemSchema')->find($schemaid);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRItemSchemaType(), $itemSchema);
        $handler = new \EIP\HRBundle\Form\HRItemSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);

        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_items'));

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRItemSchema',
                    'form' => $form->createView()
        ));
    }

    /**
     * \brief delete a item schema
     * @return View
     */
    public function deleteItemAction($schemaid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRItemSchema')->find($schemaid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($schema);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_adm_items'));
    }

    //////
    // HRBuffSchema
    /////

    /**
     * \brief list all the different buff schema
     * @return View
     */
    public function buffsAction() {
        $schemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuffSchema')->findAll();
        return $this->render('EIPHRBundle:Admin:buffs.html.twig', array(
                    'schemas' => $schemas,
        ));
    }

    /**
     * \brief add a buff schema
     * @return View
     */
    public function addBuffAction() {
        $buffSchema = new \EIP\HRBundle\Entity\HRBuffSchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRBuffSchemaType(), $buffSchema);
        $handler = new \EIP\HRBundle\Form\HRBuffSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_buffs'));

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRBuffSchema',
                    'form' => $form->createView()
        ));
    }

    /**
     * \brief edit a buff schema
     * @return View
     */
    public function editBuffAction($schemaid) {
        $buffSchema = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuffSchema')->find($schemaid);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRBuffSchemaType(), $buffSchema);
        $handler = new \EIP\HRBundle\Form\HRBuffSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);

        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_buffs'));

        return $this->render('EIPHRBundle:Admin:addEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRBuffSchema',
                    'form' => $form->createView()
        ));
    }

    /**
     * \brief delete a buff schema
     * @return View
     */
    public function deleteBuffAction($schemaid) {
        $schema = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuffSchema')->find($schemaid);
        $em = $this->getDoctrine()->getManager();
        $em->remove($schema);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_adm_buffs'));
    }

    // quests

    /**
     * \brief Lists all the quests
     * @return View
     */
    public function questsAction() {
        $schemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuestSchema')->getAllSchemas();
        return $this->render('EIPHRBundle:Admin:quests.html.twig', array(
                    'schemas' => $schemas
        ));
    }

    /**
     * \brief displays the quest edit form
     * @param integer $schemaid
     * @return View
     */
    public function editQuestAction($schemaid) {
        $questSchema = $this->getDoctrine()->getRepository('EIPHRBundle:HRQuestSchema')->find($schemaid);
        $form = $this->createForm(new \EIP\HRBundle\Form\HRQuestSchemaType(), $questSchema);
        $handler = new \EIP\HRBundle\Form\HRQuestSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), false);

        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_quests'));

        return $this->render('EIPHRBundle:Admin:AddEdit.html.twig', array(
                    'formType' => 'Edit',
                    'entityType' => 'HRQuestSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
     * \brief displays the quest add form
     * @return view
     */
    public function addQuestAction() {
        $questSchema = new \EIP\HRBundle\Entity\HRQuestSchema();
        $form = $this->createForm(new \EIP\HRBundle\Form\HRQuestSchemaType(), $questSchema);

        $handler = new \EIP\HRBundle\Form\HRQuestSchemaHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(), true);

        if ($handler->process())
            return $this->redirect($this->generateUrl('hr_adm_quests'));

        return $this->render('EIPHRBundle:Admin:AddEdit.html.twig', array(
                    'formType' => 'Add',
                    'entityType' => 'HRQuestSchema',
                    'form' => $form->createView(),
        ));
    }

    /**
     * \brief deletes a quest based on its id
     * @param integer $schemaid
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteQuestAction($schemaid) {
        try {
            $em = $this->getDoctrine()->getManager();
            $schema = $em->find('EIPHRBundle:HRQuestSchema', $schemaid);
            $em->remove($schema);
            $em->flush();
            return $this->redirect($this->generateUrl('hr_adm_quests'));
        } catch (\Exception $e) {
            return new \Symfony\Component\HttpFoundation\Response($e->getMessage());
        }
    }

    /**
     * \brief display the edit quest data form - to modify the requirements of the quest
     * @param int $schemaid
     * @return View
     */
    public function editQuestDataAction($schemaid) {
        $em = $this->getDoctrine()->getManager();
        $schema = $em->find('EIPHRBundle:HRQuestSchema', $schemaid);
        if ($this->getRequest()->getMethod() == 'POST') {
            $data = $this->getRequest()->request->get('data');
            foreach ($data as $key => $value) {
                $data[$key] = intval($value);
            }
            $schema->setData($data);
            $em->flush();
            return $this->redirect($this->generateUrl('hr_adm_quests'));
        }
        $unitSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRUnitSchema')->findAll();
        $buildingSchemas = $this->getDoctrine()->getRepository('EIPHRBundle:HRBuildingSchema')->findAll();
        return $this->render('EIPHRBundle:Admin:editQuestData.html.twig', array(
                    'unitSchemas' => $unitSchemas,
                    'buildingSchemas' => $buildingSchemas,
                    'schema' => $schema,
        ));
    }

}

