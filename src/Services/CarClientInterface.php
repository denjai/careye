<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;

interface CarClientInterface
{
    public function getCarInfo(string $id): CarResult;
}