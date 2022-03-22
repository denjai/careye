<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use App\Exception\CarInfoServerException;
use InvalidArgumentException;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpFoundation\Response;

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

        if ($this->browser->getResponse()->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new InvalidArgumentException(sprintf('Car info for card %s not found;', $crawler->getUri()));
        }

        if ($this->browser->getResponse()->getStatusCode() === Response::HTTP_BAD_GATEWAY) {
            throw new CarInfoServerException(sprintf('Car info server not responding  %s ;', self::BASE_URL));
        }


        return $this->mobileParser->parse($crawler)->setRemoteId($id);
    }

    private function buildUrl(string $id): string
    {
        return sprintf(self::BASE_URL, $id);
    }
}