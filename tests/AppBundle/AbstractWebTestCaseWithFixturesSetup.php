<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

abstract class AbstractWebTestCaseWithFixturesSetup extends WebTestCase
{
    protected static $application;

    public static function setUpBeforeClass()
    {
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:migrations:migrate');
        self::runCommand('doctrine:fixtures:load');
    }

    public static function tearDownAfterClass()
    {
        self::runCommand('doctrine:database:drop --force');
        self::$application = null;
    }

    protected static function runCommand($command)
    {
        $command = sprintf('%s -n --quiet', $command);

        return self::getApplication()->run(new StringInput($command));
    }

    protected static function getApplication()
    {
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }

        return self::$application;
    }
}
