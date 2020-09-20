<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\HttpClientFactory;

use Symfony\Contracts\HttpClient\HttpClientInterface;

interface HttpClientFactory
{
    public function build(
        string $baseUrl,
        string $apiKey
    ): HttpClientInterface;
}
