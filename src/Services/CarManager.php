<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Car;
use App\Entity\CarResult;
use App\Event\CarUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CarManager
{
    private CarHistoryFactory $carHistoryFactory;
    private EntityManagerInterface $entityManager;
    private CarResultTransformer $carResultTransformer;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        CarHistoryFactory $carHistoryFactory,
        EntityManagerInterface $entityManager,
        CarResultTransformer $carResultTransformer,
        EventDispatcherInterface $dispatcher
    ) {
        $this->carHistoryFactory = $carHistoryFactory;
        $this->entityManager = $entityManager;
        $this->carResultTransformer = $carResultTransformer;
        $this->dispatcher = $dispatcher;
    }

    public function createCar(CarResult $carResult, UserInterface $user)
    {
        $car = $this->carResultTransformer
            ->transform($carResult)
            ->setUser($user)
        ;
        $this->entityManager->persist($car);

        foreach ($this->carResultTransformer->transformImages($carResult) as $image){
            $this->entityManager->persist($image->setCar($car));
        }

        $carHistory = $this->carHistoryFactory->createFromCarResult($carResult, $car);
        $this->entityManager->persist($carHistory);
    }

    public function updatePrice(Car $car, CarResult $carResult): void
    {
        if (!$carResult->getPrice()->isEqual($car->getPrice())) {
            $car->setPrice($carResult->getPrice());

            $carHistory = $this->carHistoryFactory->createFromCarResult($carResult, $car);
            $this->entityManager->persist($carHistory);

            $this->dispatchCardPriceUpdatedEvent($car);
        }
    }

    public function updateDates(Car $car, CarResult $carResult): void
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

    public function changeStatus(Car $car, string $newStatus): void
    {
        if (!in_array($newStatus, Car::STATUSES)) {
            return;
        }

        $car->setStatus($newStatus);

        $carHistory = $this->carHistoryFactory->createFromCar($car);
        $this->entityManager->persist($carHistory);
    }

    private function dispatchCardPriceUpdatedEvent($car): void
    {
        $this->dispatcher->dispatch(new CarUpdatedEvent($car), CarUpdatedEvent::NAME_PRICE_UPDATED);
    }
}