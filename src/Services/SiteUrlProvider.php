<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

class SiteUrlProvider
{
    private array $siteUrlTemplates;
    private string $defaultApiKey;

    public function __construct(array $siteUrlTemplates, string $defaultApiKey)
    {
        $this->siteUrlTemplates = $siteUrlTemplates;
        $this->defaultApiKey = $defaultApiKey;
    }

    public function getUrl(string $id, string $provider = null): string
    {
        if ($provider === null) {
            $provider = $this->defaultApiKey;
        }

        if (!isset($this->siteUrlTemplates[$provider])) {
            throw new InvalidArgumentException('There is no url configured for ' . $provider);
        }

        return sprintf($this->siteUrlTemplates[$provider], $id);
    }
}