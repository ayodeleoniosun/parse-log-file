<?php

namespace App\Repository;

use App\Entity\LogAnalytics;
use App\Repository\Interfaces\LogAnalyticsRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

class LogAnalyticsRepository extends BaseEntityRepository implements LogAnalyticsRepositoryInterface
{
    private LogAnalytics $logAnalytics;

    public function __construct(ManagerRegistry $registry, LogAnalytics $logAnalytics)
    {
        parent::__construct($registry, LogAnalytics::class);
        $this->logAnalytics = $logAnalytics;
    }

    public function save(object $data): void
    {
        $this->logAnalytics->setServiceName($data->service_name);
        $this->logAnalytics->setStartDate($data->start_date);
        $this->logAnalytics->setEndDate($data->end_date);
        $this->logAnalytics->setStatusCode($data->status_code);
        $this->logAnalytics->setCreatedAt($data->created_at);
        $this->logAnalytics->setUpdatedAt($data->updated_at);

        $this->persistDatabase($this->logAnalytics);
    }

    public function retrieve(Request $request): void
    {
        dd($request);
    }

    public function remove(LogAnalytics $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

//    /**
//     * @return LogAnalytics[] Returns an array of LogAnalytics objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?LogAnalytics
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
