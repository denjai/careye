<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Car;
use App\Entity\CarResult;
use Doctrine\ORM\EntityManagerInterface;
use Evp\Component\Money\Money;

class CarManager
{
    private CarHistoryFactory $carHistoryFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(CarHistoryFactory $carHistoryFactory, EntityManagerInterface $entityManager)
    {
        $this->carHistoryFactory = $carHistoryFactory;
        $this->entityManager = $entityManager;
    }

    public function updatePrice(Car $car, CarResult $carResult)
    {
        if (!$carResult->getPrice()->isEqual($car->getPrice())) {
            $car->setPrice($carResult->getPrice());

            $carHistory = $this->carHistoryFactory->createFromCarResult($carResult, $car);
            $this->entityManager->persist($carHistory);
        }
    }

    public function updateDates(Car $car, CarResult $carResult)
    {
        if (($carResult->getUpdatedAt() !== null && $car->getUpdated() === null ) ||
            $car->getUpdated() !== null && $carResult->getUpdatedAt() !== null &&
            $car->getUpdated()->format('Y-m-d') !== $carResult->getUpdatedAt()->format('Y-m-d')
        ) {
            $car->setUpdated($carResult->getUpdatedAt());

            $carHistory = $this->carHistoryFactory->createFromCarResult($carResult, $car);
            $this->entityManager->persist($carHistory);
        }
    }
}