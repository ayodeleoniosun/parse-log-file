<?php

declare(strict_types=1);

namespace Tests\Service;

use App\DataFixtures\LogAnalyticsFixture;
use App\Service\LogAnalyticsService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Tests\Trait\LogAnalytics as LogAnalyticsTrait;

class LogAnalyticsServiceTest extends KernelTestCase
{
    use LogAnalyticsTrait;

    private EntityManager $entityManager;

    private ?object $logService;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->logService = $container->get(LogAnalyticsService::class);
    }

    public function testStoreLogAnalytics(): void
    {
        $analytics = $this->generateLogAnalytics('service');
        $this->logService->store($analytics);
        $this->assertTrue(true);
    }

    public function testFilterLogAnalytics(): void
    {
        $dateTime = date('Y-m-d H:i:s');

        $fixture = new LogAnalyticsFixture();
        $fixture->load($this->entityManager);

        $request = new Request();

        $request->query->add([
            'serviceNames' => ['user-service', 'invoice-service'],
            'statusCode'   => 201,
            'startDate'    => $dateTime,
        ]);

        $response = $this->logService->filter($request);
        $this->assertGreaterThan(0, count($response));
    }

    public function testFormatDate(): void
    {
        $dateTime = date('Y-m-d H:i:s');
        $formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);

        $response = $this->logService->formatDate($dateTime);

        $this->assertEquals($response, $formattedDateTime);
    }
}
