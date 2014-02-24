<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * \class InitForumCommand
 * \brief This command set up the main sections for the forum and create the topics_search table
 */
class InitForumCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hr:initForum')
            ->setDescription('initialise le forum');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine')->getManager();
        // create the forum sections
        $sectionInfos = array('forum.general' => 'forum.general.description',
            'forum.technical' => 'forum.technical.description',
            'forum.everything.else' => 'forum.everything.else.description'
            );
        $repo = $em->getRepository('EIPHRBundle:HRForumSection');
        foreach ($sectionInfos as $key => $value) {
            $e = $repo->findOneBy(array('name' => $key));
            if ($e != null)
            {
                $output->writeln($key." section already exists, passing.");
                continue;
            }
            $section = new \EIP\HRBundle\Entity\HRForumSection();
            $section->setName($key);
            $section->setDescription($value);
            $em->persist($section);
            $output->writeln("creating section: ".$key);
        }
        $em->flush();
        $output->writeln("InitForum: Done.");
    }
}
