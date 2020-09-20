<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\HttpClientFactory;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SymfonyHttpClientFactory implements HttpClientFactory
{
    public function build(
        string $baseUrl,
        string $apiKey
    ): HttpClientInterface {
        return HttpClient::createForBaseUri(rtrim($baseUrl), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'authorization' => sprintf('Bearer %s', $apiKey),
            ],
        ]);
    }
}
