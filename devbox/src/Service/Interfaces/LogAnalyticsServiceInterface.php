<?php

namespace App\Service\Interfaces;

use Symfony\Component\HttpFoundation\Request;

interface LogAnalyticsServiceInterface
{
    public function store(array $analytics): void;

    public function filter(Request $request): array;
}
