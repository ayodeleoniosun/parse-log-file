<?php

declare(strict_types=1);

namespace Tests\Controller;

use App\DataFixtures\LogAnalyticsFixture;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogAnalyticsControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private $entityManager;

    private array $analytics;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $fixture = new LogAnalyticsFixture();
        $this->analytics = $fixture->load($this->entityManager);
    }

    public function testRetrieveAllLogAnalytics(): void
    {
        $this->client->request('GET', '/count');
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertEquals(count($this->analytics), $content->counter);
    }

    public function testFilterByServiceNamesOnly(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service');
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = count(array_filter($this->analytics, function ($analytics) {
            return in_array($analytics['service_name'], ['user-service', 'invoice-service']);
        }));

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    public function testFilterByServiceNamesAndStatusCode(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200');
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = count(array_filter($this->analytics, function ($analytics) {
            return in_array($analytics['service_name'], ['user-service', 'invoice-service'])
                && $analytics['status_code'] === 200;
        }));

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    public function testFilterByServiceNamesAndStatusCodeAndStartDate(): void
    {
        $dateTime = date('Y-m-d H:i:s');

        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200&startDate=' . $dateTime);
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = count(array_filter($this->analytics, function ($analytics) use ($dateTime) {
            return
                in_array($analytics['service_name'], ['user-service', 'invoice-service'])
                && $analytics['status_code'] === 200
                && $analytics['start_date'] == \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        }));

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    public function testFilterByAllCriteria(): void
    {
        $dateTime = date('Y-m-d H:i:s');

        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200&startDate=' . $dateTime . '&endDate=' . $dateTime);
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = count(array_filter($this->analytics, function ($analytics) use ($dateTime) {
            return
                in_array($analytics['service_name'], ['user-service', 'invoice-service'])
                && $analytics['status_code'] === 200
                && $analytics['start_date'] == \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime)
                && $analytics['end_date'] == \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        }));

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
