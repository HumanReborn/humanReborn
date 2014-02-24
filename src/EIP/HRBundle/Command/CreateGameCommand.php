<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
*  \brief Create a HRGame, closed by default
*/
class CreateGameCommand extends ContainerAwareCommand {

	protected function configure() {
		$this->setName('hr:createGame')
		->setDescription('Create a new HRGame, closed by default')
		->addArgument('name', InputArgument::REQUIRED, 'Name of the game ?')
		->addArgument('opened', InputArgument::OPTIONAL);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {
		$em = $this->getContainer()->get('doctrine')->getManager();
		$game = new \EIP\HRBundle\Entity\HRGame();
		$gameName = $input->getArgument('name');
		$game->setName($gameName);
		$status = $input->getArgument('opened') ? \EIP\HRBundle\Entity\HRGame::STATUS_OPENED : \EIP\HRBundle\Entity\HRGame::STATUS_CLOSED;
		$game->setStatus($status);
		// validate game model (unique name ...)
		$error = $this->getContainer()->get('validator')->validate($game);
		if (count($error) > 0) {
			$output->writeln("Error(s) encoutered -- script aborted:");
			foreach ($error as $k => $e)
				$output->writeln('Error['.$k.']: '.$e);
			return ;
		}

		$em->persist($game);
		// adding the neutral player
		$neutralPlayer = $em->getRepository('EIPHRBundle:HRUser')->getNeutralPlayer();
		$gamelink = new \EIP\HRBundle\Entity\HRGameLink();
		$gamelink->setUser($neutralPlayer);
		$gamelink->setGame($game);
		$em->persist($gamelink);
		// adding the towns to the game
		$towns = $this->getContainer()->get('town_factory')->getXFromResourceFile(90);
		foreach ($towns as $town) {
			$town->setGame($game);
			$town->setOwner($neutralPlayer);
			$em->persist($town);
		}

		$output->writeln('Game: '.$game->getName().' created.');
		$em->flush();
	}

}