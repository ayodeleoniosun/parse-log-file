<?php

namespace App\Repository\Interfaces;

interface LogAnalyticsRepositoryInterface
{
    public function save(array $analytics): int;

    public function filter(?array $serviceNames, ?string $startDate, ?string $endDate, ?int $statusCode): array;
}
