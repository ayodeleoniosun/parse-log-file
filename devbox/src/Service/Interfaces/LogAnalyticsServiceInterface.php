<?php

namespace App\Service\Interfaces;

use App\Entity\LogAnalytics;

interface LogAnalyticsServiceInterface
{
    public function store(object $data): LogAnalytics;
}
