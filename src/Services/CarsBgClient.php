<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use Symfony\Component\BrowserKit\HttpBrowser;

class CarsBgClient implements CarClientInterface
{
    public const BASE_URL = 'https://www.cars.bg/offer/%s';

    private HttpBrowser $browser;
    private CarsBgParser $carsBgParser;

    public function __construct(HttpBrowser $browser, CarsBgParser $carsBgParser)
    {
        $this->browser = $browser;
        $this->carsBgParser = $carsBgParser;
    }

    public function getCarInfo(string $id): CarResult
    {
        $crawler = $this->browser
            ->request('GET', $this->buildUrl($id))
        ;

        return $this->carsBgParser->parse($crawler)->setRemoteId($id);
    }

    private function buildUrl(string $id): string
    {
        return sprintf(self::BASE_URL, $id);
    }
}