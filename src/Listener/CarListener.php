<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\CarUpdatedEvent;
use App\Services\CarDiscordNotificationSender;

class CarListener
{
    private CarDiscordNotificationSender $carDiscordNotificationSender;

    public function __construct(CarDiscordNotificationSender $carDiscordNotificationSender)
    {
        $this->carDiscordNotificationSender = $carDiscordNotificationSender;
    }

    public function onCarPriceUpdated(CarUpdatedEvent $carUpdatedEvent)
    {
        $this->carDiscordNotificationSender->sendPriceUpdatedNotification($carUpdatedEvent->getCar());
    }
}