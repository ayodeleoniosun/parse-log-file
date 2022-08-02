<?php

declare(strict_types=1);

namespace Tests\Controller;

use App\DataFixtures\LogAnalyticsFixture;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Trait\LogAnalytics as LogAnalyticsTrait;

class LogAnalyticsControllerTest extends WebTestCase
{
    use LogAnalyticsTrait;

    private KernelBrowser $client;

    private $entityManager;

    private array $analytics;

    private array $serviceNames;

    private int $statusCode;

    private string $dateTime;

    private \DateTime $formattedDateTime;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $fixture = new LogAnalyticsFixture();
        $this->analytics = $fixture->load($this->entityManager);
        $this->serviceNames = ['user-service', 'invoice-service'];
        $this->statusCode = 200;
        $this->dateTime = date('Y-m-d H:i:s');
        $this->formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $this->dateTime);
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

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNamesOnly($this->analytics, $this->serviceNames);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    public function testFilterByServiceNamesAndStatusCode(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200');
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCode($this->analytics, $this->serviceNames, $this->statusCode);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    public function testFilterByServiceNamesAndStatusCodeAndStartDate(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200&startDate=' . $this->dateTime);
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCodeAndStartDate($this->analytics, $this->serviceNames, $this->statusCode, $this->formattedDateTime);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($countFilterAnalytics, $content->counter);
    }

    public function testFilterByAllCriteria(): void
    {
        $this->client->request('GET', '/count?serviceNames[]=user-service&serviceNames[]=invoice-service&statusCode=200&startDate=' . $this->dateTime . '&endDate=' . $this->dateTime);
        $content = json_decode($this->client->getResponse()->getContent());

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCodeAndStartDateAndEndDate($this->analytics, $this->serviceNames, $this->statusCode, $this->formattedDateTime);

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
