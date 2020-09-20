<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Exception\ApiError;

use Fivem\ClashOfClans\Exception\ApiClientExceptionInterface;

class UnknownApiErrorException extends \Exception implements ApiClientExceptionInterface
{
    private int $responseStatusCode;
    private string $responseContent;

    public function __construct(int $responseStatusCode, string $responseContent, \Throwable $previous)
    {
        parent::__construct(
            sprintf('An unknown error occurred (status code = %s) : %s', $statusCode, $previous->getMessage()),
            0,
            $previous
        );

        $this->responseStatusCode = $responseStatusCode;
        $this->responseContent = $responseContent;
    }

    public function getResponseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function getResponseContent(): string
    {
        return $this->responseContent;
    }
}
