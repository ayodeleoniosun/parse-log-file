<?php

declare(strict_types=1);

namespace Tests\Repository;

use App\DataFixtures\LogAnalyticsFixture;
use App\Entity\LogAnalytics;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Trait\LogAnalytics as LogAnalyticsTrait;

class LogAnalyticsRepositoryTest extends KernelTestCase
{
    use LogAnalyticsTrait;

    private $logRepo;

    private $entityManager;

    private array $analytics;

    private array $serviceNames;

    private int $statusCode;

    private string $dateTime;

    private \DateTime $formattedDateTime;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();

        $fixture = new LogAnalyticsFixture();
        $this->analytics = $fixture->load($this->entityManager);
        $this->serviceNames = ['user-service', 'invoice-service'];
        $this->statusCode = 201;
        $this->dateTime = date('Y-m-d H:i:s');
        $this->formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $this->dateTime);

        $this->logRepo = $this->entityManager->getRepository(LogAnalytics::class);
    }

    public function testSaveLogAnalytics(): void
    {
        $records = 20;
        $analytics = $this->generateLogAnalytics('repository', $records);
        $countInserted = $this->logRepo->save($analytics);

        $this->assertEquals($records, $countInserted);
    }

    public function testFilterAnalyticsByServiceNamesOnly(): void
    {
        $response = $this->logRepo->filter($this->serviceNames, null, null, null);

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNamesOnly($this->analytics, $this->serviceNames);

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function testFilterAnalyticsByServiceNamesAndStatusCode(): void
    {
        $response = $this->logRepo->filter($this->serviceNames, $this->statusCode, null, null);

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCode($this->analytics, $this->serviceNames, $this->statusCode);

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function testFilterByServiceNamesAndStatusCodeAndStartDate(): void
    {
        $response = $this->logRepo->filter($this->serviceNames, $this->statusCode, $this->dateTime, null);

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCodeAndStartDate($this->analytics, $this->serviceNames, $this->statusCode, $this->formattedDateTime);

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function testFilterByAllCriteria(): void
    {
        $response = $this->logRepo->filter($this->serviceNames, $this->statusCode, $this->dateTime, $this->dateTime);

        $countFilterAnalytics = $this->countFilterByAllCriteria($this->analytics, $this->serviceNames, $this->statusCode, $this->formattedDateTime);

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
