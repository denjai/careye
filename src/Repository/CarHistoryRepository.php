<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Car;
use App\Entity\CarHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CarHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CarHistory::class);
    }

    public function getPreviousPrice(Car $car): ?CarHistory
    {
        return $this->createQueryBuilder('ch')
            ->where('ch.car = :car')
            ->setParameter('car', $car)
            ->orderBy('ch.id', 'DESC')
            ->setMaxResults(1)
            ->setFirstResult(2)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}