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

    public function save(array $analytics): int
    {
        $queryBuilder = $this->createQueryBuilder('log')->orderBy('log.id', 'DESC');
        $allAnalytics = $queryBuilder->getQuery()->getArrayResult();

        if (count($allAnalytics) == 0) {
            $insertions = 1;
        } else {
            $lastAnalytics = $queryBuilder->setMaxResults(1)->getQuery()->getArrayResult();
            $insertions = $lastAnalytics[0]['insertions'] + 1;
        }

        for ($i = 0; $i < count($analytics); $i++) {
            $data = $analytics[$i];

            $logAnalytics = new LogAnalytics();

            $logAnalytics->setServiceName($data->service_name);
            $logAnalytics->setStartDate($data->start_date);
            $logAnalytics->setEndDate($data->end_date);
            $logAnalytics->setStatusCode($data->status_code);
            $logAnalytics->setCreatedAt($data->created_at);
            $logAnalytics->setUpdatedAt($data->updated_at);
            $logAnalytics->setInsertions($insertions);

            $this->persist($logAnalytics);
            $this->flush();
        }

        return $queryBuilder->select('count(log.id)')
            ->where('log.insertions = :insertions')
            ->setParameter('insertions', $insertions)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function filter(
        ?array  $serviceNames,
        ?int    $statusCode,
        ?string $startDate,
        ?string $endDate
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('log');

        if (count($serviceNames) > 0) {
            $queryBuilder->andWhere('log.service_name IN (:service_name)')
                ->setParameter('service_name', $serviceNames);
        }

        if ($statusCode) {
            $queryBuilder->andWhere('log.status_code = :status_code')
                ->setParameter('status_code', $statusCode);
        }

        if ($startDate) {
            $queryBuilder->andWhere('log.start_date = :start_date')
                ->setParameter('start_date', $startDate);
        }

        if ($endDate) {
            $queryBuilder->andWhere('log.end_date = :end_date')
                ->setParameter('end_date', $endDate);
        }

        return $queryBuilder->orderBy('log.id')->getQuery()->getArrayResult();
    }
}
