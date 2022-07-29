<?php

namespace App\DataFixtures;

use App\Entity\LogAnalytics;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LogAnalyticsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
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

            $manager->persist($logAnalytics);
        }

        $manager->flush();
    }
}
