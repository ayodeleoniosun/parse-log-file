<?php

namespace App\Repository\Interfaces;

use App\Entity\LogAnalytics;

interface LogAnalyticsRepositoryInterface
{
    public function save(object $data): LogAnalytics;
}
