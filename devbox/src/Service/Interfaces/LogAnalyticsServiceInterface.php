<?php

namespace App\Service\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface LogAnalyticsServiceInterface
{
    public function store(array $analytics): int;

    public function filter(Request $request): array;
}
