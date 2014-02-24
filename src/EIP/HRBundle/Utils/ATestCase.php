<?php


namespace EIP\HRBundle\Utils;

require_once __DIR__.'/../../../../app/AppKernel.php';

/**
	\class ATestCase
	\brief base class for unit tests

*/
class ATestCase extends \PHPUnit_Framework_TestCase
{
    protected static $kernel;
    protected static $container;
    protected static $application;
    protected static $validator;
    protected static $doctrine;

    protected function setUp ()
    {
        parent::setUp();
    }

    public function tearDown(){
        self::$container->get('doctrine')->getConnection()->close();
        parent::tearDown();
    }

    public static function setUpBeforeClass()
    {
        self::$kernel = new \AppKernel('test', true);
        self::$kernel->boot();

        self::$container = self::$kernel->getContainer();
        self::$application = new \Symfony\Bundle\FrameworkBundle\Console\Application(self::$kernel);
        self::$application->setAutoExit(true);
        self::$validator = self::$container->get('validator');
        self::$doctrine = self::$container->get('doctrine');
    }

    protected function runConsole($command, Array $options = array())
    {
        $options = array_merge($options,array('command' => $command));
        return self::$application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }
}
