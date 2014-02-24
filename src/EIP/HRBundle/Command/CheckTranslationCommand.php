<?php

namespace EIP\HRBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * \class CheckTranslationCommand
 * \brief This command checks if french and english translation are up to date, and for double entries
 */
class CheckTranslationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('hr:ct')
            ->setDescription('Verifie l\'etat des traductions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        foreach (array('messages', 'quests', 'items', 'units', 'technologies') as $file)

        {
            $frYaml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents('src/EIP/HRBundle/Resources/translations/'.$file.'.fr.yml'));
            $enYaml = \Symfony\Component\Yaml\Yaml::parse(file_get_contents('src/EIP/HRBundle/Resources/translations/'.$file.'.en.yml'));

            $usedValues = array();
            $this->checkMissingKeys($frYaml, $enYaml, $file, $usedValues, $output);
            $this->checkDoubleValues($usedValues, $file, $output);
        }
        $output->writeln('Done');
    }

    private function checkDoubleValues($usedValues, $file, OutputInterface $output)
    {
        $output->writeln('=>');
        $arr = array_count_values($usedValues);
        foreach ($arr as $k=>$e) {
            if ($e > 1) {
                $output->writeln("Doublon pour : $k\t\tin file: $file");
            }
        }
        $output->writeln('======');
    }

    private function addToUsedValues(&$usedValues, $value)
    {
        if (is_array($value))
        {
            foreach ($value as $k => $v)
            {
                $this->addToUsedValues($usedValues, $v);
            }
        }
        else
        {
            $usedValues[] = $value;
        }
    }

    private function checkMissingKeys($frYaml, $enYaml, $file, &$usedValues, OutputInterface $output)
    {
        foreach ($frYaml as $key => $value)
        {
            if (array_key_exists($key, $enYaml) === false){
                $output->writeln("Key: $key does not exist in $file.en.yml");
            }
            $this->addToUsedValues($usedValues, $value);
        }
        $output->writeln('----');
        foreach ($enYaml as $key => $value)
        {
            if (array_key_exists($key, $frYaml) === false){
                $output->writeln("Key: $key does not exist in $file.fr.yml");
            }
        }
    }
}