<?php

namespace App\Service;

use App\Repository\Interfaces\LogAnalyticsRepositoryInterface;
use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class LogAnalyticsService implements LogAnalyticsServiceInterface
{
    protected LogAnalyticsRepositoryInterface $logRepo;

    public function __construct(LogAnalyticsRepositoryInterface $logRepo)
    {
        $this->logRepo = $logRepo;
    }

    public function store(array $analytics): int
    {
        $allAnalytics = array_map(function ($data) {
            $data->start_date = $this->formatDate($data->start_date);
            $data->end_date = $this->formatDate($data->end_date);
            $data->created_at = new \DateTime('now');
            $data->updated_at = new \DateTime('now');

            return $data;
        }, $analytics);

        return $this->logRepo->save($allAnalytics);
    }

    public function filter(Request $request): array
    {
        $serviceNames = (array)$request->get('serviceNames') ?? null;
        $startDate = $request->get('startDate') ?? null;
        $endDate = $request->get('endDate') ?? null;
        $statusCode = (int)$request->get('statusCode') ?? null;

        return $this->logRepo->filter($serviceNames, $statusCode, $startDate, $endDate);
    }

    public function formatDate($date): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }
}
