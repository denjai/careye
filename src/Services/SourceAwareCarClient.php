<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use InvalidArgumentException;

class SourceAwareCarClient
{
    /**
     * @var CarClientInterface[]
     */
    private array $clients;

    public function __construct(array $clients)
    {
        $this->clients = $clients;
    }

    public function getCarInfo(string $id, string $source): CarResult
    {
        if (!isset($this->clients[$source])) {
            throw new InvalidArgumentException('Invalid source provided');
        }

        return $this->clients[$source]->getCarInfo($id)->setSource($source);
    }
}