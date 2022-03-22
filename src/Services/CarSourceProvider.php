<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class CarSourceProvider
{
    private array $sourceMap;

    public function __construct(array $sourceMap)
    {
        $this->sourceMap = $sourceMap;
    }

    public function getSource(string $url)
    {
        foreach ($this->sourceMap as $source => $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $source;
            }
        }

        throw new InvalidArgumentException('Invalid url. Source not found in source map.');
    }
}