<?php

namespace App\Service;

use App\Entity\LogAnalytics;
use App\Repository\Interfaces\LogAnalyticsRepositoryInterface;
use App\Service\Interfaces\LogAnalyticsServiceInterface;

class LogAnalyticsService implements LogAnalyticsServiceInterface
{
    protected LogAnalyticsRepositoryInterface $logRepo;

    public function __construct(LogAnalyticsRepositoryInterface $logRepo)
    {
        $this->logRepo = $logRepo;
    }

    public function store(object $data): LogAnalytics
    {
        return $this->logRepo->save($data);
    }
}
