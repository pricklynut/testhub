<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\StringInput;

class ApplicationAvailabilityFunctionalTest extends WebTestCase
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
    }

    public function testHomepageIsSuccessful()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/');

        $items = $crawler->filter('.test-item');

        $this->assertGreaterThan(0, $items->count());
        $this->assertEquals($items->count(), $items->filter('.title')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testTestsListPageIsSuccessful()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/tests');

        $items = $crawler->filter('.test-item');

        $this->assertGreaterThan(0, $items->count());
        $this->assertEquals($items->count(), $items->filter('.title')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testTestsListPageWithNotFoundSearch()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/tests?search=loremipsumabracadabra');

        $items = $crawler->filter('.test-item');
        $flashNotFound = $crawler->filter('.bg-warning');

        $this->assertEquals(0, $items->count());
        $this->assertEquals(1, $flashNotFound->count());
        $this->assertTrue($client->getResponse()->isSuccessful());
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
