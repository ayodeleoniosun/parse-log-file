<?php

declare(strict_types=1);

namespace Tests\Service;

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

    private array $analytics;

    private array $serviceNames;

    private int $statusCode;

    private string $dateTime;

    private \DateTime $formattedDateTime;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $this->analytics = $this->generateLogAnalytics('service');
        $this->serviceNames = ['user-service', 'invoice-service'];
        $this->statusCode = 201;
        $this->dateTime = date('Y-m-d H:i:s');
        $this->formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $this->dateTime);

        $this->logService = $container->get(LogAnalyticsService::class);
    }

    public function testStoreLogAnalytics(): void
    {
        $countRecords = $this->logService->store($this->analytics);
        $this->assertEquals(count($this->analytics), $countRecords);
    }

    public function testFilterAnalyticsByServiceNamesOnly(): void
    {
        $this->logService->store($this->analytics);

        $request = new Request();
        $request->query->add(['serviceNames' => $this->serviceNames]);

        $response = $this->logService->filter($request);

        $this->analytics = $this->formatAnalytics($this->analytics);

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNamesOnly($this->analytics, $this->serviceNames);

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function testFilterAnalyticsByServiceNamesAndStatusCode(): void
    {
        $this->logService->store($this->analytics);

        $request = new Request();

        $request->query->add([
            'serviceNames' => $this->serviceNames,
            'statusCode'   => $this->statusCode,
        ]);

        $response = $this->logService->filter($request);

        $this->analytics = $this->formatAnalytics($this->analytics);

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCode(
            $this->analytics,
            $this->serviceNames,
            $this->statusCode
        );

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function testFilterByServiceNamesAndStatusCodeAndStartDate(): void
    {
        $this->logService->store($this->analytics);

        $request = new Request();

        $request->query->add([
            'serviceNames' => $this->serviceNames,
            'statusCode'   => $this->statusCode,
            'startDate'    => $this->dateTime,
        ]);

        $response = $this->logService->filter($request);

        $this->analytics = $this->formatAnalytics($this->analytics);

        $countFilterAnalytics = $this->countFilterAnalyticsByServiceNameAndStatusCodeAndStartDate(
            $this->analytics,
            $this->serviceNames,
            $this->statusCode,
            $this->formattedDateTime
        );

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function testFilterByAllCriteria(): void
    {
        $this->logService->store($this->analytics);

        $request = new Request();

        $request->query->add([
            'serviceNames' => $this->serviceNames,
            'statusCode'   => $this->statusCode,
            'startDate'    => $this->dateTime,
            'endDate'      => $this->dateTime,
        ]);

        $response = $this->logService->filter($request);

        $this->analytics = $this->formatAnalytics($this->analytics);

        $countFilterAnalytics = $this->countFilterByAllCriteria(
            $this->analytics,
            $this->serviceNames,
            $this->statusCode,
            $this->formattedDateTime
        );

        $this->assertEquals($countFilterAnalytics, count($response));
    }

    public function formatAnalytics($analytics): array
    {
        array_walk($analytics, function (&$item) {
            $item = (array) $item;
        });

        return $analytics;
    }
}
