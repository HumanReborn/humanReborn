<?php

namespace EIP\HRBundle\Utils;
use EIP\HRBundle\Entity\HRUser;
use EIP\HRBundle\Entity\HRTown;
use EIP\HRBundle\Entity\HRGame;
use EIP\HRBundle\Entity\HRGameLink;
use EIP\HRBundle\Entity\HRArmy;
use EIP\HRBundle\Entity\HRHero;
use EIP\HRBundle\Entity\HRHeroSchema;
use EIP\HRBundle\Entity\HRQuestSchema;
use EIP\HRBundle\Entity\HRQuestGameLink;

/**
* \brief Offers helpers to manage the test games
*/
class TestGame {

    /**
    *   \brief creates a test game for a user
    * @param HRUser $user User for which the game will be created
    * @param EntityManager $em
    * @return HRGame
    */
    public static function createTestGame(HRUser $user, $em)
    {
        //  game
        $game = new HRGame();
        $game->setName($user->getUsername().'-test-game');
        $game->setStatus(HRGame::STATUS_OPENED);
        $game->setPrivate(true);
        $em->persist($game);
        // towns
        $humanrebornUser = $em->getRepository('EIPHRBundle:HRUser')->getNeutralPlayer();
        $towns = array(
                       array('name' => 'Edge', 'x' => 100, 'y' => '300'),
                       array('name' => 'Zlum', 'x' => 300, 'y' => '300'),
                       array('name' => 'Vroum', 'x' => 500, 'y' => '600'),
                       array('name' => 'Bok', 'x' => 650, 'y' => '550')
                       );
        $townCount = count($towns);
        for ($i = 0; $i < $townCount; $i++)
        {
            $t = new HRTown();
            $t->setName($towns[$i]['name']);
            $t->setXCoord($towns[$i]['x']);
            $t->setYCoord($towns[$i]['y']);
            $owner = ($i < 2) ? $user : $humanrebornUser;
            $t->setOwner($owner);
            $t->setGame($game);
            // garrisons
            $gar = new HRArmy($user, $game, $t);
            $gar->setGarrison(true);

            $em->persist($t);
            $em->persist($gar);
        }

        // default general hero
        $heroSchema = $em->getRepository('EIPHRBundle:HRHeroSchema')->findOneBy(array('name' => 'general'));
        foreach (array($user, $humanrebornUser) as $u)
        {
            // gamelink
            $gl = new HRGameLink();
            $gl->setUser($u);
            $gl->setGame($game);
            $em->persist($gl);
            // hero
            $hero = new HRHero();
            $hero->setSchema($heroSchema);
            $hero->setGame($game);
            $hero->setUser($u);
            $gl->setHero($hero);
            $em->persist($hero);
            // resources
            $gl->setResources(HRGameLink::createResourcesArray(2500));
        }


        // quests
        $qss = $em->getRepository('EIPHRBundle:HRQuestSchema')->findAll();
        foreach ($qss as $qs)
        {
            $ql = new \EIP\HRBundle\Entity\HRQuestGameLink($qs, $game);
            $em->persist($ql);
        }
        //
        $em->flush();
        return $game;
    }
}