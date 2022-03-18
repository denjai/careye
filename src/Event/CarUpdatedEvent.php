<?php

declare(strict_types=1);

namespace App\Event;

use App\Entity\Car;
use Symfony\Contracts\EventDispatcher\Event;

class CarUpdatedEvent extends Event
{
    public const NAME_PRICE_UPDATED = 'car.price_updated';

    private Car $car;

    public function __construct(Car $car)
    {
        $this->car = $car;
    }

    public function getCar(): Car
    {
        return $this->car;
    }
}