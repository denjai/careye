<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Evp\Component\Money\Money;
use Symfony\Component\Security\Core\User\UserInterface;

class Car
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUSES = [
        self::STATUS_ACTIVE => self::STATUS_ACTIVE,
        self::STATUS_CLOSED => self::STATUS_CLOSED,
    ];


    private int $id;
    private string $remoteId;
    private UserInterface $user;
    private string $title;
    private string $amount;
    private string $currency;
    private ?DateTimeImmutable $created;
    private ?DateTimeImmutable $updated;
    private string $status;
    private string $source;
    private Collection $images;

    public function __construct()
    {
        $this->created = null;
        $this->updated = null;
        $this->status = self::STATUS_ACTIVE;
        $this->images = new ArrayCollection();
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

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): self
    {
        $this->user = $user;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
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

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        $this->images[] = $image;
        return $this;
    }
}