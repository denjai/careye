<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use Symfony\Component\DomCrawler\Crawler;

class CarsBgParser
{
    public function parse(Crawler $crawler): CarResult
    {
        return new CarResult();
    }
}