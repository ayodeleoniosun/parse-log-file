<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

abstract class BaseEntityRepository extends ServiceEntityRepository
{
    protected ObjectManager $entityManager;

    protected ObjectRepository $repository;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
        $this->entityManager = $this->getEntityManager();
    }

    protected function persistDatabase($entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
