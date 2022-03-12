<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use Symfony\Component\BrowserKit\HttpBrowser;

class CarClient
{
    public const BASE_URL = 'https://www.mobile.bg/pcgi/mobile.cgi?act=4&adv=%s';

    private HttpBrowser $browser;
    private CarInfoParser $carInfoParser;

    public function __construct(HttpBrowser $browser, CarInfoParser $carInfoParser)
    {
        $this->browser = $browser;
        $this->carInfoParser = $carInfoParser;
    }

    public function getCarInfo(string $id): CarResult
    {
        $crawler = $this->browser
            ->request('GET', $this->buildUrl($id))
            ->filter('form[name="search"]')
        ;

        return $this->carInfoParser->parse($crawler);
    }

    private function buildUrl(string $id): string
    {
        return sprintf(self::BASE_URL, $id);
    }
}