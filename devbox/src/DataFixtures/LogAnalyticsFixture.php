<?php

namespace App\DataFixtures;

use App\Entity\LogAnalytics;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LogAnalyticsFixture extends Fixture
{
    public function load(ObjectManager $manager): array
    {
        $dateTime = new \DateTime('now');

        $serviceNames = ['user-service', 'invoice-service'];
        $statusCodes = [200, 201, 500];

        for ($i = 1; $i <= 20; $i++) {
            $serviceNameKey = array_rand($serviceNames);
            $codeKey = array_rand($statusCodes);

            $logAnalytics = new LogAnalytics();
            $logAnalytics->setServiceName($serviceNames[$serviceNameKey]);
            $logAnalytics->setStartDate($dateTime);
            $logAnalytics->setEndDate($dateTime);
            $logAnalytics->setStatusCode($statusCodes[$codeKey]);
            $logAnalytics->setCreatedAt($dateTime);
            $logAnalytics->setUpdatedAt($dateTime);
            $logAnalytics->setInsertions(1);

            $manager->persist($logAnalytics);
        }

        $manager->flush();

        $repository = $manager->getRepository(LogAnalytics::class);

        $queryBuilder = $repository->createQueryBuilder('log')->orderBy('log.id', 'DESC');

        return $queryBuilder->where('log.insertions = :insertions')
            ->setParameter('insertions', 1)
            ->getQuery()
            ->getArrayResult();
    }
}
