<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\CarResult;
use DateTimeImmutable;
use Evp\Component\Money\Money;
use InvalidArgumentException;
use Symfony\Component\DomCrawler\Crawler;

class MobileParser
{
    private MonthMapper $monthMapper;

    public function __construct(MonthMapper $monthMapper)
    {
        $this->monthMapper = $monthMapper;
    }

    public function parse(Crawler $crawler): CarResult
    {
        if ($crawler->filter('#details_price')->count() === 0) {
            throw new InvalidArgumentException(sprintf('Car info for card %s not found;', $crawler->getUri()));
        }

        $result = (new CarResult())
            ->setPrice($this->getPrice($crawler))
            ->setTitle($crawler->filter('div table h1')->text())
        ;

        $createdAt = $this->getCreatedAt($crawler);
        if ($createdAt !== null) {
            $result->setCratedAt($createdAt);
        }

        $updatedAt = $this->getUpdatedAt($crawler);
        if ($updatedAt !== null) {
            $result->setUpdatedAt($updatedAt);
        }

        return $result;
    }

    private function getPrice(Crawler $crawler): Money
    {
        $moneyString = $crawler->filter('#details_price')->text('0 лв.', true);

        preg_match('/лв.|USD|EUR/', $moneyString, $matches);
        $amount = trim(preg_replace('/лв.|USD|EUR|\s/', '', $moneyString));
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

    private function getCreatedAt(Crawler $crawler): ?DateTimeImmutable
    {
        $dateString = $crawler->filter('div:nth-of-type(2) div:nth-of-type(3) span:first-of-type')->text();

        if (preg_match('/Публикувана/', $dateString, $matches) === 1) {
            $time = '00:00';
            if (preg_match('/\d{2}:\d{2}/', $dateString, $matches)){
                $time = $matches[0];
            }
            if (preg_match('/\d{1,2}\s\D{2,30}\s\d{4}/iu', $dateString, $matches) === 1) {
                return new DateTimeImmutable($this->monthMapper->getMonth($matches[0] . $time));
            }
        }

        return null;
    }

    private function getUpdatedAt(Crawler $crawler): ?DateTimeImmutable
    {
        $dateString = $crawler->filter('div:nth-of-type(2) div:nth-of-type(3) span:first-of-type')->text();

        if (preg_match('/Редактирана/', $dateString, $matches) === 1) {
            $time = '00:00';
            if (preg_match('/\d{2}:\d{2}/', $dateString, $matches) === 1){
                $time = $matches[0];
            }

            if (preg_match('/\d{1,2}\s\D{2,30}\s\d{4}/iu', $dateString, $matches) === 1) {
                return new DateTimeImmutable($this->monthMapper->getMonth($matches[0] . $time));
            }
        }

        return null;
    }
}