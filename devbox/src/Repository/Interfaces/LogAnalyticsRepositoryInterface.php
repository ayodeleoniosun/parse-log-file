<?php

namespace App\Repository\Interfaces;

interface LogAnalyticsRepositoryInterface
{
    public function save(array $analytics): void;

    public function filter(?array $serviceNames, ?string $startDate, ?string $endDate, ?int $statusCode): array;
}
