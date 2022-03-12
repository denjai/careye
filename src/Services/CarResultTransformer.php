<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Car;
use App\Entity\CarResult;

class CarResultTransformer
{
    public function transform(CarResult $carResult): Car
    {
        return (new Car())
            ->setTitle($carResult->getTitle())
            ->setPrice($carResult->getPrice())
            ->setCreated($carResult->getCratedAt())
            ->setUpdated($carResult->getUpdatedAt())
        ;
    }
}