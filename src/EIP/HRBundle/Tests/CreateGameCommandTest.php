<?php

namespace EIP\HRBundle\Tests;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use EIP\HRBundle\Command\CreateGameCommand;
use EIP\HRBundle\Utils\TestTools;
use EIP\HRBundle\Utils\ATestCase as ATestCase;

class CreateGameCommandTest extends ATestCase {

    public function testExecute() {
        $em = self::$doctrine->getManager();
        TestTools::clearTables($em);
        $application = self::$application;
        $application->add(new CreateGameCommand());
        $command = $application->find('hr:createGame');
        $commandTester = new CommandTester($command);

        // inserting neutral player
        $user = new \EIP\HRBundle\Entity\HRUser;
        $user->setUsername('HumanReborn');
        $user->setEmail('test@gmail.com');
        $user->setPassword('randompassword');
        $em->persist($user);
        $em->flush();

        // no town before the command
        $nbTown = $em->createQuery("SELECT COUNT(t.id) FROM EIPHRBundle:HRTown t LEFT JOIN t.game g WHERE g.name = :gameName")
        ->setParameter(':gameName', 'myTestName')
        ->getSingleScalarResult();
        $this->assertEquals(0, $nbTown);
        // no game with that name
        $game = $em->createQuery("SELECT g FROM EIPHRBundle:HRGame g WHERE g.name = :gameName")
        ->setParameter(':gameName', 'myTestName')
        ->getOneOrNullResult();
        $this->assertNull($game);
        // executing the command
        $commandTester->execute(array('command' => $command->getName(), 'name' => 'myTestName', 'opened' => false));
        // expected to find 'created' in the output
        $this->assertRegExp('/created/', $commandTester->getDisplay());
        $nbTown = $em->createQuery("SELECT COUNT(t.id) FROM EIPHRBundle:HRTown t LEFT JOIN t.game g WHERE g.name = :gameName")
        ->setParameter('gameName', 'myTestName')
        ->getSingleScalarResult();
        // 90 towns expected for the created game
        $this->assertEquals(90, $nbTown);
        // One game with that name
        $game = $em->createQuery("SELECT g FROM EIPHRBundle:HRGame g WHERE g.name = :gameName")
        ->setParameter(':gameName', 'myTestName')
        ->getOneOrNullResult();
        $this->assertNotNull($game);
        $this->assertEquals(\EIP\HRBundle\Entity\HRGame::STATUS_CLOSED, $game->getStatus());
    }

}