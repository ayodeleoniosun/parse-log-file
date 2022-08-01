<?php

namespace App\Repository;

use App\Entity\LogAnalytics;
use App\Repository\Interfaces\LogAnalyticsRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

class LogAnalyticsRepository extends BaseEntityRepository implements LogAnalyticsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogAnalytics::class);
    }

    public function save(array $analytics): void
    {
        $batchSize = 20;

        for ($i = 0; $i < count($analytics); $i++) {
            $data = $analytics[$i];

            $logAnalytics = new LogAnalytics();

            $logAnalytics->setServiceName($data->service_name);
            $logAnalytics->setStartDate($data->start_date);
            $logAnalytics->setEndDate($data->end_date);
            $logAnalytics->setStatusCode($data->status_code);
            $logAnalytics->setCreatedAt($data->created_at);
            $logAnalytics->setUpdatedAt($data->updated_at);

            $this->persist($logAnalytics);

            if (($i % $batchSize) === 0) {
                $this->flush();
            }
        }

        $this->flush();
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
