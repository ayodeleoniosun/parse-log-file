<?php

namespace App\Repository\Interfaces;

interface LogAnalyticsRepositoryInterface
{
    public function save(array $analytics): int;

    public function filter(?array $serviceNames, ?int $statusCode, ?string $startDate, ?string $endDate): array;
}
