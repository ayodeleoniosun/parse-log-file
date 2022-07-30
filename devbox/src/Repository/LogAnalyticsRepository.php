<?php

namespace App\Repository;

use App\Entity\LogAnalytics;
use App\Repository\Interfaces\LogAnalyticsRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class LogAnalyticsRepository extends BaseEntityRepository implements LogAnalyticsRepositoryInterface
{
    private LogAnalytics $logAnalytics;

    public function __construct(ManagerRegistry $registry, LogAnalytics $logAnalytics)
    {
        parent::__construct($registry, LogAnalytics::class);
        $this->logAnalytics = $logAnalytics;
    }

    public function save(object $data): LogAnalytics
    {
        $this->logAnalytics->setServiceName($data->service_name);
        $this->logAnalytics->setStartDate($data->start_date);
        $this->logAnalytics->setEndDate($data->end_date);
        $this->logAnalytics->setStatusCode($data->status_code);
        $this->logAnalytics->setCreatedAt($data->created_at);
        $this->logAnalytics->setUpdatedAt($data->updated_at);

        $this->persistDatabase($this->logAnalytics);

        return $this->find($this->logAnalytics->getId());
    }

    public function filter(?array $serviceNames, ?string $startDate, ?string $endDate, ?int $statusCode): array
    {
        $queryBuilder = $this->createQueryBuilder('log');

        if (count($serviceNames) > 0) {
            $queryBuilder->andWhere('log.service_name IN (:service_name)')
                ->setParameter('service_name', $serviceNames);
        }

        if ($startDate) {
            $queryBuilder->andWhere('log.start_date = :start_date')
                ->setParameter('start_date', $startDate);
        }

        if ($endDate) {
            $queryBuilder->andWhere('log.end_date = :end_date')
                ->setParameter('end_date', $endDate);
        }

        if ($statusCode) {
            $queryBuilder->andWhere('log.status_code = :status_code')
                ->setParameter('status_code', $statusCode);
        }

        return $queryBuilder->orderBy('log.id')->getQuery()->getArrayResult();
    }
}
