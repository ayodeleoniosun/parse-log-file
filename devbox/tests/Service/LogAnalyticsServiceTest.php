<?php
declare(strict_types=1);

namespace Tests\Service;

use App\DataFixtures\LogAnalyticsFixture;
use App\Service\LogAnalyticsService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class LogAnalyticsServiceTest extends KernelTestCase
{
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
        $dateTime = date("Y-m-d H:i:s");

        $data = (object)[
            'service_name' => 'user-service',
            'start_date'   => $dateTime,
            'end_date'     => $dateTime,
            'status_code'  => 201,
        ];

        $response = $this->logService->store($data);

        $this->assertEquals($response->getServiceName(), $data->service_name);
        $this->assertEquals($response->getStartDate(), $data->start_date);
        $this->assertEquals($response->getEndDate(), $data->end_date);
        $this->assertEquals($response->getStatusCode(), $data->status_code);
    }

    public function testFilterLogAnalytics(): void
    {
        $dateTime = date("Y-m-d H:i:s");

        $fixture = new LogAnalyticsFixture();
        $fixture->load($this->entityManager);

        $request = new Request();

        $request->query->add([
            'serviceNames' => ['user-service', 'invoice-service'],
            'statusCode'   => 201,
            'startDate'    => $dateTime
        ]);

        $response = $this->logService->filter($request);
        $this->assertGreaterThan(0, count($response));
    }

    public function testFormatDate(): void
    {
        $dateTime = date("Y-m-d H:i:s");
        $formattedDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);

        $response = $this->logService->formatDate($dateTime);

        $this->assertEquals($response, $formattedDateTime);
    }
}
