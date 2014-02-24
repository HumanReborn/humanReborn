<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * \class RemoveAllCommand
 * \brief This command clears the database content
 */
class RemoveAllCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hr:removeAll')
            ->setDescription('Suppression de la BDD');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $output->writeln("Databases: Clearing ....");

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
        $output->writeln("Databases: Cleared.");
    }
}
