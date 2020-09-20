<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model;

class ApiError implements Model
{
    private const STATUS_CODES_REASONS = [
        400 => 'Client provided incorrect parameters for the request.',
        403 => 'Access denied, either because of missing/incorrect credentials or used API token does not grant access to the requested resource.',
        404 => 'Resource was not found.',
        429 => 'Request was throttled, because amount of requests was above the threshold defined for the used API token.',
        500 => 'Unknown error happened when handling the request.',
        503 => 'Service is temporarily unavailable because of maintenance.',
    ];

    public string $reason;
    public ?string $message = null;

    public function getCustomReason(int $statusCode): string
    {
        if (400 === $statusCode) {
            return $this->message;
        }

        if (\array_key_exists($statusCode, self::STATUS_CODES_REASONS)) {
            return self::STATUS_CODES_REASONS[$statusCode];
        }

        if ($this->reason) {
            return $this->reason;
        }

        return $this->message ?: sprintf('No error message fopr status code = %d', $statusCode);
    }
}
