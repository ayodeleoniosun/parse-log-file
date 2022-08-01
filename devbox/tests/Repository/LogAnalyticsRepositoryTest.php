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

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->logRepo = $this->entityManager->getRepository(LogAnalytics::class);
    }

    public function testSaveLogAnalytics(): void
    {
        $analytics = $this->generateLogAnalytics('repository');
        $this->logRepo->save($analytics);
        $this->assertTrue(true);
    }

    public function testFilterLogAnalytics(): void
    {
        $fixture = new LogAnalyticsFixture();
        $fixture->load($this->entityManager);

        $serviceNames = ['user-service', 'invoice-service'];
        $statusCode = 200;

        $response = $this->logRepo->filter($serviceNames, null, null, $statusCode);
        $this->assertGreaterThan(0, count($response));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
