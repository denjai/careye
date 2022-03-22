<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Evp\Component\Money\Money;

class CarResult
{
    private Money $price;
    private string $title;
    private ?DateTimeImmutable $cratedAt;
    private ?DateTimeImmutable $updatedAt;
    private string $remoteId;
    private string $source;

    public function __construct()
    {
        $this->cratedAt = null;
        $this->updatedAt = null;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function setPrice(Money $price): self
    {
        $this->price = $price;
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

    public function getCratedAt(): ?DateTimeImmutable
    {
        return $this->cratedAt;
    }

    public function setCratedAt(?DateTimeImmutable $cratedAt): self
    {
        $this->cratedAt = $cratedAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
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

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        return $this;
    }
}