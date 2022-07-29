<?php

namespace App\Service;

use App\Entity\LogAnalytics;
use App\Repository\Interfaces\LogAnalyticsRepositoryInterface;
use App\Service\Interfaces\LogAnalyticsServiceInterface;
use Symfony\Component\HttpFoundation\Request;

class LogAnalyticsService implements LogAnalyticsServiceInterface
{
    protected LogAnalyticsRepositoryInterface $logRepo;

    public function __construct(LogAnalyticsRepositoryInterface $logRepo)
    {
        $this->logRepo = $logRepo;
    }

    public function store(object $data): void
    {
        $data->start_date = $this->formatDate($data->start_date);
        $data->end_date = $this->formatDate($data->end_date);
        $data->created_at = new \DateTime('now');
        $data->updated_at = new \DateTime('now');

        $this->logRepo->save($data);
    }

    public function count(Request $request): void
    {
        $this->logRepo->retrieve($request);
    }

    public function formatDate($date): \DateTime
    {
        return \DateTime::createFromFormat('Y-m-d H:i:s', $date);
    }
}
