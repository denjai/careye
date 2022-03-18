<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\CarUpdatedEvent;

class CarListener
{
    public function onCarPriceUpdated(CarUpdatedEvent $carUpdatedEvent)
    {
        //var_dump($carUpdatedEvent->getCar());
        //TODO send notification
    }
}