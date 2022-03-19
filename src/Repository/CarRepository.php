<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    /**
     * @return iterable|Car[]
     */
    public function getAllActiveIterableResult()
    {
        return $this->createQueryBuilder('c')
            ->where('c.status = :status')
            ->setParameter('status', Car::STATUS_ACTIVE)
            ->getQuery()
            ->toIterable()
        ;
    }
}