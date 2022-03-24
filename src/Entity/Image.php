<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;

class Image
{
    private int $id;
    private $image;
    private string $extension;
    private DateTimeImmutable $created;
    private Car $car;

    public function __construct()
    {
        $this->created = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getImage(): string
    {
        rewind($this->image);
        return stream_get_contents($this->image);
    }

    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;
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

    public function getCar(): Car
    {
        return $this->car;
    }

    public function setCar(Car $car): self
    {
        $this->car = $car;
        return $this;
    }
}