<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Car;
use App\Entity\CarHistory;
use App\Entity\CarResult;

class CarHistoryFactory
{
    public function createFromCarResult(CarResult $carResult, Car $car): CarHistory
    {
        return (new CarHistory())
            ->setPrice($carResult->getPrice())
            ->setAdvertUpdated($carResult->getUpdatedAt())
            ->setCar($car)
        ;
    }
}