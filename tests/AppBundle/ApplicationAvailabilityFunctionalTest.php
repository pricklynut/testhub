<?php

namespace Tests\AppBundle;

use Symfony\Component\BrowserKit\Cookie;

class ApplicationAvailabilityFunctionalTest extends AbstractWebTestCaseWithFixturesSetup
{
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

    public function testTestPrefaceIsSuccessful()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/test/8/preface');

        $this->assertGreaterThan(0, $crawler->filter('h1')->count());

        $startTestButton = $crawler->filter('.buttons a')->last();
        $this->assertContains('Начать тест', $startTestButton->text());
        $this->assertEquals('/test/8/start', $startTestButton->attr('href'));

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testStartAttemptReturnRedirect()
    {
        $client = self::createClient();
        $client->request('GET', '/test/8/start');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        return $client;
    }

    /**
     * @depends testStartAttemptReturnRedirect
     */
    public function testQuestionPageWhenAuthorizedIsSuccessful($client)
    {
        $cookieSetInPreviousAction = $client->getCookieJar()->get('guest_key');
        $this->assertGreaterThan(0, strlen($cookieSetInPreviousAction));

        $crawler = $client->request('GET', '/test/8/question/1');

        $this->assertGreaterThan(0, $crawler->filter('input')->count());
        $this->assertTrue($client->getResponse()->isSuccessful());

        return $client;
    }

    public function testQuestionPageWhenUnauthorizedReturnRedirect()
    {
        $client = self::createClient();
        $client->request('GET', '/test/8/question/1');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * @param $client
     *
     * @depends testQuestionPageWhenAuthorizedIsSuccessful
     */
    public function testFinishAttemptPageIsSuccessful($client)
    {
        $client->request('GET', '/test/8/finish');

        $this->assertTrue($client->getResponse()->isSuccessful());

        return $client;
    }

    /**
     * @param $client
     *
     * @depends testFinishAttemptPageIsSuccessful
     */
    public function testResultPageIsSuccessful($client)
    {
        $client->request('GET', '/test/8/result');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testCreateTestPageIsSuccessful()
    {
        $client = self::createClient();
        $client->request('GET', '/test/new');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testEditTestPageIsForbiddenWhenNoPermissions()
    {
        $client = self::createClient();
        $client->request('GET', '/test/8/edit');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testEditTestPageIsSuccessfulWhenUserIsAuthor()
    {
        $client = self::createClient();
        $cookie = new Cookie('guest_key', 'qwerty12345');
        $client->getCookieJar()->set($cookie);
        $client->request('GET', '/test/8/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}
