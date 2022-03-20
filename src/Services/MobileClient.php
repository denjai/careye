<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use Symfony\Component\BrowserKit\HttpBrowser;

class MobileClient implements CarClientInterface
{
    public const BASE_URL = 'https://www.mobile.bg/pcgi/mobile.cgi?act=4&adv=%s';

    private HttpBrowser $browser;
    private MobileParser $mobileParser;

    public function __construct(HttpBrowser $browser, MobileParser $mobileParser)
    {
        $this->browser = $browser;
        $this->mobileParser = $mobileParser;
    }

    public function getCarInfo(string $id): CarResult
    {
        $crawler = $this->browser
            ->request('GET', $this->buildUrl($id))
            ->filter('form[name="search"]')
        ;

        return $this->mobileParser->parse($crawler)->setRemoteId($id);
    }

    private function buildUrl(string $id): string
    {
        return sprintf(self::BASE_URL, $id);
    }
}