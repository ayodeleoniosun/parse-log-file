<?php

namespace App\Service\Interfaces;

use App\Entity\LogAnalytics;
use Symfony\Component\HttpFoundation\Request;

interface LogAnalyticsServiceInterface
{
    public function store(object $data): LogAnalytics;

    public function filter(Request $request): array;
}
