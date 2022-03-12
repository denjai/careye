<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Evp\Component\Money\Money;

class Car
{
    private int $id;
    private string $remoteId;
    private string $title;
    private string $amount;
    private string $currency;
    private ?DateTimeImmutable $created;
    private ?DateTimeImmutable $updated;

    public function __construct()
    {
        $this->created = null;
        $this->updated = null;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getRemoteId(): string
    {
        return $this->remoteId;
    }

    public function setRemoteId(string $remoteId): self
    {
        $this->remoteId = $remoteId;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
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

    public function getCreated(): ?DateTimeImmutable
    {
        return $this->created;
    }

    public function setCreated(?DateTimeImmutable $created): self
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?DateTimeImmutable
    {
        return $this->updated;
    }

    public function setUpdated(?DateTimeImmutable $updated): self
    {
        $this->updated = $updated;
        return $this;
    }
}