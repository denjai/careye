<?php

declare(strict_types=1);

namespace App\Services;

class MonthMapper
{
    private array $patterns;
    private array $months;

    public function __construct()
    {
        $this->months = [
            'january',
            'february',
            'march',
            'april',
            'may',
            'june',
            'july',
            'august',
            'september',
            'october',
            'november',
            'december',
        ];

        $this->patterns = [
            '/януари/',
            '/февруари/',
            '/март/',
            '/април/',
            '/май/',
            '/юни/',
            '/юли/',
            '/август/',
            '/септември/',
            '/октомври/',
            '/ноември/',
            '/декември/',
        ];
    }

    public function getMonth(string $month): string
    {
        return preg_replace($this->patterns, $this->months, $month);
    }
}