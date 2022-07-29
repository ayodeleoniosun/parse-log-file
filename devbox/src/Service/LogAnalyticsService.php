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
        $data->start_date = \DateTime::createFromFormat('Y-m-d H:i:s', $data->start_date);
        $data->end_date = \DateTime::createFromFormat('Y-m-d H:i:s', $data->end_date);
        $data->created_at = new \DateTime('now');
        $data->updated_at = new \DateTime('now');

        return $this->logRepo->save($data);
    }

    public function formatDate($date)
    {

    }
}
