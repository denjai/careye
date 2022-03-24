<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use DateTime;
use DateTimeImmutable;
use Evp\Component\Money\Money;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class CarsBgParser
{
    public function parse(Crawler $crawler): CarResult
    {
        if ($crawler->filter('.main-content')->count() === 0) {
            throw new InvalidArgumentException(sprintf('Car info for card %s not found;', $crawler->getUri()));
        }

        $result = (new CarResult())
            ->setPrice($this->getPrice($crawler))
            ->setTitle($crawler->filter('.offer-price + div h2')->text(''))
        ;

        $createdAt = $this->getCreatedAt($crawler);
        if ($createdAt !== null) {
            $result->setCratedAt($createdAt);
        }

        foreach ($this->getImages($crawler) as $image) {
            $result->addImage($image);
        }

        return $result;
    }

    private function getPrice(Crawler $crawler): Money
    {
        $moneyString = $crawler->filter('.offer-price')->text('0 лв.', true);

        preg_match('/лв.|USD|EUR/', $moneyString, $matches);
        $amount = trim(preg_replace('/лв.|USD|EUR|\s|,/', '', $moneyString));
        $currency = $this->getMappedCurrency($matches[0]);

        return Money::create($amount, $currency);
    }

    private function getMappedCurrency(string $currency): string
    {
        switch ($currency) {
            case  'лв.':
                return 'BGN';
            default:
                return $currency;
        }
    }

    private function getCreatedAt(Crawler $crawler)
    {
        $dateString = $crawler->filter('.content table td div')->text();

        $today = (new DateTime())->format('Y-m-d');
        $dateString = preg_replace('/днес,/', $today , $dateString);

        $yesterday = (new DateTime())->modify('-1 day')->format('Y-m-d');
        $dateString = preg_replace('/вчера/', $yesterday , $dateString);

        $dateString = preg_replace('/&nbsp;/', '' , trim(htmlentities($dateString)));

        if (preg_match('/\d{2}\.\d{2}\.\d{2}/', $dateString, $matches)) {
            return DateTimeImmutable::createFromFormat('d.m.y', trim($dateString))->setTime(0, 0, 0);
        }

        return new DateTimeImmutable($dateString);
    }

    private function getImages(Crawler $crawler): array
    {
        return $crawler->filter('#smallgallery img')->extract(['data-src']);
    }
}