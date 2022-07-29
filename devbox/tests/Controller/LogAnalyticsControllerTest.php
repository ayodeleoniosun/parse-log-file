<?php

namespace Tests\Controller;

use App\DataFixtures\LogAnalyticsFixture;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LogAnalyticsControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private $entityManager;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $fixture = new LogAnalyticsFixture();
        $fixture->load($this->entityManager);
    }

    public function testRetrieveAllLogAnalytics(): void
    {
        $this->client->request('GET', '/count');
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $content->counter);
    }

    public function testFilterByServiceNamesOnly(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service');
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $content->counter);
    }

    public function testFilterByServiceNamesAndStatusCode(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200');
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $content->counter);
    }

    public function testFilterByServiceNamesAndStatusCodeAndStartDate(): void
    {
        $dateTime = date("Y-m-d H:i:s");

        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200&startDate=' . $dateTime);
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $content->counter);
    }

    public function testFilterByAllCriteria(): void
    {
        $dateTime = date("Y-m-d H:i:s");

        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200&startDate=' . $dateTime . '&endDate=' . $dateTime);
        $content = json_decode($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $content->counter);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
