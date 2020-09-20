<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Exception;

use Fivem\ClashOfClans\Model\ApiError;

class ApiErrorException extends \Exception implements ApiClientExceptionInterface
{
    private int $responseStatusCode;
    private string $responseContent;
    private ApiError $apiError;

    public function __construct(int $responseStatusCode, string $responseContent, ApiError $apiError)
    {
        parent::__construct($apiError->getCustomReason($responseStatusCode));

        $this->responseStatusCode = $responseStatusCode;
        $this->responseContent = $responseContent;
        $this->apiError = $apiError;
    }

    public function getResponseStatusCode(): int
    {
        return $this->responseStatusCode;
    }

    public function getResponseContent(): string
    {
        return $this->responseContent;
    }

    public function getApiError(): ApiError
    {
        return $this->apiError;
    }

    public function isNotFound(): bool
    {
        return 404 === $this->responseStatusCode;
    }
}
