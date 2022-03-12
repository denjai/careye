<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Evp\Component\Money\Money;

class CarHistory
{
    private int $id;
    private Car $car;
    private string $amount;
    private string $currency;
    private ?DateTimeImmutable $advertUpdated;
    private DateTimeImmutable $created;

    public function __construct()
    {
        $this->created = new DateTimeImmutable();
        $this->advertUpdated = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCar(): Car
    {
        return $this->car;
    }

    public function setCar(Car $car): CarHistory
    {
        $this->car = $car;
        return $this;
    }

    public function getPrice(): Money
    {
        return Money::create($this->amount, $this->currency);
    }

    public function setPrice(Money $price): self
    {
        $this->amount = $price->getAmount();
        $this->currency = $price->getCurrency();
        return $this;
    }

    public function getAdvertUpdated(): ?DateTimeImmutable
    {
        return $this->advertUpdated;
    }

    public function setAdvertUpdated(?DateTimeImmutable $advertUpdated): self
    {
        $this->advertUpdated = $advertUpdated;
        return $this;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(DateTimeImmutable $created): self
    {
        $this->created = $created;
        return $this;
    }
}