<?php

namespace App\Repository\Interfaces;

use App\Entity\LogAnalytics;

interface LogAnalyticsRepositoryInterface
{
    public function save(object $data): LogAnalytics;

    public function filter(?array $serviceNames, ?string $startDate, ?string $endDate, ?int $statusCode): array;
}
