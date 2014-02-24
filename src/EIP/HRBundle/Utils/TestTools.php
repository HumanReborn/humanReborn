<?php

namespace EIP\HRBundle\Utils;

/**
*	\class TestTools
*	\brief regroup usefull function for unit tests
*/
class TestTools {

	/**
	*	\fn HRUser getValidUser(string, string, string)
	*	\brief return a valid HRUser filled with the provided username, password and email
	*	@param string $username
	*	@param string $password
	*	@param string $email
	*	@param EncoderFactory $factory
	*	@return HRUser $user
	*/
    public static function getValidUser($username, $password, $email, $factory = null)
    {
        $user = new \EIP\HRBundle\Entity\HRUser();
        $user->setUsername($username);
		if ($factory)
			$password = $factory->getEncoder($user)->encodePassword($password, $user->getSalt());
        $user->setPassword($password);
        $user->setEmail($email);
        return $user;
    }

	/**
	*	\fn HRBuildingSchema getValidBuildingSchema(string)
	*	\brief return a valid HRBuildingSchema filled with the provided informations
	*	@param string $schemaName
	*	@return HRBuildingSchema $schema
	*/
    public static function getValidBuildingSchema($schemaName)
    {
        $schema = new \EIP\HRBundle\Entity\HRBuildingSchema();
        $schema->setBuildingRequirement(0);
        $schema->setTechnologyRequirement(0);
        $schema->setRValue(0);
        $schema->setName($schemaName);
        return $schema;
    }

	/**
	*	\fn HRTown getValidTown(string, HRGame, HRUser, int, int)
	*	\brief return a valid HRTown filled with the provided informations
	*	@param string $schemaName
	*	@param HRGame $game
	*	@param HRUser $user
	*	@param int $x
	*	@param int $y
	*	@return HRTown $town
	*/
    public static function getValidTown($townName, $game, $user, $xCoord = 0, $yCoord= 0)
    {
        $town = new \EIP\HRBundle\Entity\HRTown();
        $town->setName($townName);
        $town->setGame($game);
        $town->setOwner($user);
        $town->setXCoord($xCoord);
        $town->setYCoord($yCoord);
        return $town;
    }

	/**
	*	\fn HRGame getValidGame(string)
	*	\brief return a valid HRGame filled with the provided informations
	*	@param string $gameName
	*	@param int $status
	*	@return HRGame $game
	*/
    public static function getValidGame($gameName, $status = null)
    {
        $game = new \EIP\HRBundle\Entity\HRGame();
        $game->setName($gameName);
        if ($status)
            $game->setStatus($status);
        return $game;
    }

	/**
	*	\fn HRTechnologySchema getValidTechnologySchema(string)
	*	\brief return a valid HRTechnologySchema filled with the provided informations
	*	@param string $schemaName
	*	@return HRTechnologySchema $schema
	*/
    public static function getValidTechnologySchema($schemaName)
    {
        $schema = new \EIP\HRBundle\Entity\HRTechnologySchema();
        $schema->setBuildingRequirement(0);
        $schema->setTechnologyRequirement(0);
        $schema->setRValue(0);
        $schema->setName($schemaName);
        $schema->setType(1);
        return $schema;
    }

	/**
	*	\fn HRGameLink getValidGameLink(HRUser, HRGame)
	*	\brief return a valid HRGameLink filled with the provided informations
	*	@param HRUser $user
	*	@param HRGame $game
	*	@return HRGameLink $gameLink
	*/
    public static function getValidGameLink($user, $game)
    {
        $gl = new \EIP\HRBundle\Entity\HRGameLink();
        $gl->setGame($game);
        $gl->setUser($user);
        return $gl;
    }

	/**
	*	\fn HRArmy getValidGarrison(HRTown)
	*	\brief return a valid HRArmy filled with the provided informations
	*	@param HRTown $town
	*	@return HRArmy $army
	*/
    public static function getValidGarrison(\EIP\HRBundle\Entity\HRTown $town)
    {
        $army = new \EIP\HRBundle\Entity\HRArmy();
        $army->setGame($town->getGame());
        $army->setUser($town->getOwner());
        $army->setTown($town);
		$army->setGarrison(true);
        return $army;
    }

	/**
	*	\fn HRArmy getValidArmy(HRTown)
	*	\brief return a valid HRArmy filled with the provided informations
	*	@param HRTown $town
	*	@return HRArmy $army
	*/
    public static function getValidArmy(\EIP\HRBundle\Entity\HRTown $town)
    {
        $army = new \EIP\HRBundle\Entity\HRArmy($town->getOwner(), $town->getGame(), $town);
        return $army;
    }

	/**
	*	\fn HRBuildingQueue getValidBuildingQueue(HRTown, HRBuildingSchema, int)
	*	\brief return a valid HRBuildingQueue filled with the provided informations
	*	@param HRTown $town
	*	@param HRBuildingSchema $schema
	*	@param int $startTime
	*	@return HRBuildingQueue $buildingQueue
	*/
    public static function getValidBuildingQueue(\EIP\HRBundle\Entity\HRTown $town,
                                                                    \EIP\HRBundle\Entity\HRBuildingSchema $schema,
                                                                    $startTime = 0)
    {
        $b = new \EIP\HRBundle\Entity\HRBuildingQueue($schema, $town->getOwner(), $town->getGame(), $town, $startTime);
        return $b;
    }

	/**
	*	\fn HRBuilding getValidBuilding(HRBuildingSchema, HRTown)
	*	\brief return a valid HRBuilding filled with the provided informations
	*	@param HRBuildingSchema $schema
	*	@param HRTown $town
	*	@return HRBuilding $building
	*/
    public static function getValidBuilding(\EIP\HRBundle\Entity\HRBuildingSchema $schema,
                                                            \EIP\HRBundle\Entity\HRTown $town)
    {
        $b = new \EIP\HRBundle\Entity\HRBuilding();
        $b->setSchema($schema);
        $b->setTown($town);
        return $b;
    }

    /**
    *        \fn HRClan getValidClan(string)
    *        \brief return a valid HRClan filled with the provided informations
    *        @param string $clanName
    *        @return HRClan $clan
    */
    public static function getValidClan($clanName, \EIP\HRBundle\Entity\HRGame $game)
    {
        $c = new \EIP\HRBundle\Entity\HRClan();
        $c->setName($clanName);
        $c->setCreatedOn(new \DateTime("now"));
        $c->setIdGame($game->getId());
        $c->setPrivatePresentation("private");
        $c->setPublicPresentation("public");
        $c->setAcronym(substr($clanName, 0, 3));
        $c->setRecruitmentStatut(0);
        return $c;
    }


    /**
    *        \fn HRMessage getValidMessage(HRUser, HRUser, string, string)
    *        \brief return a valid HRMessage filled with the provided informations
    *        @param
    *        @param string $title
    *        @param string $content
    *        @return HRMessage $message
    */
    public static function getValidMessage(\EIP\HRBundle\Entity\HRUser $sender,
    \EIP\HRBundle\Entity\HRUser $receiver, $title = "testtitle", $content = "testContent")
    {
        $m = new \EIP\HRBundle\Entity\HRMessage();
        $m->setTitle($title);
        $m->setContent($content);
        $m->setSender($sender);
        $m->setReceiver($receiver);
        return $m;
    }

    /**
    *        \fn HRMessage getValidTechnology(HRTechnologySchema, HRUser, HRGame)
    *        \brief return a valid HRTechnology filled with the provided informations
    *        @param HRTechnologySchema $schema
    *        @param HRUser $user
    *        @param HRGame $game
    *        @return HRTechnology $technology
    */
    public static function getValidTechnology(\EIP\HRBundle\Entity\HRTechnologySchema $schema, $user, $game)
    {
        $t = new \EIP\HRBundle\Entity\HRTechnology();
        $t->setGame($game);
        $t->setUser($user);
        $t->setSchema($schema);
        return $t;
    }

	/**
	*	\fn HRMessage getValidUnitSchema(string, int)
	*	\brief return a valid HRUnitSchema filled with the provided informations
	*	@param string $schemaname
	*	@param int $buildtime
	*	@return HRUnitSchema $schema
	*/
    public static function getValidUnitSchema($schemaName, $buildingTime = 0)
    {
        $s = new \EIP\HRBundle\Entity\HRUnitSchema();
        $s->setBuildingTime($buildingTime);
        $s->setName($schemaName);
        $s->setImg(0);
        $s->setType(1);
        return $s;
    }

    /**
    *       \fn HRMessage getValidUnitQueue(HRUnitSchema, HRArmy, int)
    *        \brief return a valid HRUnitQueue filled with the provided informations
    *        @param HRUnitSchema $schema
    *        @param HRArmy $army
    *        @param int $startTime
    *        @return HRUnitQueue $unitQueue
    */
    public static function getValidUnitQueue(\EIP\HRBundle\Entity\HRUnitSchema $schema,
            \EIP\HRBundle\Entity\HRArmy $army, $startTime)
    {
        $q = new \EIP\HRBundle\Entity\HRUnitQueue($schema, $army, $startTime);
        return $q;
    }

    /**
    *        \fn HRMessage getValidHeroSchema(schemaName)
    *        \brief return a valid HRHeroSchema
    *        @param string $schemaName
    *        @return HRHeroSchema $s
    */
    public static function getValidHeroSchema($schemaName)
    {
        $s = new \EIP\HRBundle\Entity\HRHeroSchema();
        $s->setName($schemaName);
        return $s;
    }

    /**
    *        \fn HRMessage getValidHero(schemaName)
    *        \brief return a valid HRHero
    *        @param HRHeroSchema $s
    *        @param HRgame $game
    *        @param HRUser $user
    *        @return HRHero $h
    */
    public static function getValidHero(\EIP\HRBundle\Entity\HRHeroSchema $s, \EIP\HRBundle\Entity\HRGame $game,
        \EIP\HRBundle\Entity\HRUser $user)
    {
        $h = new \EIP\HRBundle\Entity\HRHero();
        $h->setSchema($s);
        $h->setGame($game);
        $h->setUser($user);
        return $h;
    }

    /**
    *        \fn void clearTables(EntityManager)
    *        \brief clear the content of the test database tables -- the order of the tables matters -- foreignKey
    *        @param EntityManager $em
    */
    public static function clearTables( \Doctrine\ORM\EntityManager $em )
    {
        // ! order matters !
        $tables = array(
            'HRAchievement',
            'HRAchievementSchema',
            'HRQuestGameLink',
            'HRQuest',
            'HRQuestSchema',
            'HRBattleReport',
            'HRNotification',
            'HRBuilding',
            'HRTechnology',
            'HRUnit',
            'HRArmyMovement',
            'HRBuff',
            'HRItem',
            'HRItemSchema',
            'HRGameLink',
            'HRHero',
            'HRMessage',
            'HRUnitQueue',
            'HRArmy',
            'HRBuildingQueue',
            'HRTechnologyQueue',
            'HRUnitDescription',
            'HRUnitSchema',
            'HRBuildingSchema',
            'HRTechnologySchema',
            'HRBuffSchema',
            'HRHeroSchema',
            'HRNotification',
            'HRClan',
            'LCToken',
            'ChatToken',
            'HRTown',
            'HRGame',
            'HRForumSection',
            'HRUser',
        );
        foreach ($tables as $table)
        {
            $entities = $em->getRepository('EIPHRBundle:'.$table)->findAll();
            foreach ($entities as $entity)
                $em->remove($entity);
            $em->flush();
            $em->clear();
        }
    }


}
