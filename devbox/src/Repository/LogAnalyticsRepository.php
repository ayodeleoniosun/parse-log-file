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
        $startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->start_date);
        $endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->end_date);

        $this->logAnalytics->setServiceName($data->service_name);
        $this->logAnalytics->setStartDate($startDate);
        $this->logAnalytics->setEndDate($endDate);
        $this->logAnalytics->setStatusCode($data->status_code);
        $this->logAnalytics->setCreatedAt(new \DateTime('now'));
        $this->logAnalytics->setUpdatedAt(new \DateTime('now'));

        $this->persistDatabase($this->logAnalytics);

        return $this->find($this->logAnalytics->getId());
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
