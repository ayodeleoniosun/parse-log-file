<?php
declare(strict_types=1);

namespace Tests\Repository;

use App\DataFixtures\LogAnalyticsFixture;
use App\Entity\LogAnalytics;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LogAnalyticsRepositoryTest extends KernelTestCase
{
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
        $dateTime = new \DateTime('now');

        $data = (object)[
            'service_name' => 'user-service',
            'start_date'   => $dateTime,
            'end_date'     => $dateTime,
            'status_code'  => 201,
            'created_at'   => $dateTime,
            'updated_at'   => $dateTime,
        ];

        $response = $this->logRepo->save($data);

        $this->assertEquals($response->getServiceName(), $data->service_name);
        $this->assertEquals($response->getStartDate(), $data->start_date);
        $this->assertEquals($response->getEndDate(), $data->end_date);
        $this->assertEquals($response->getStatusCode(), $data->status_code);
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
