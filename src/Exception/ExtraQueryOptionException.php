<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Exception;

class ExtraQueryOptionException extends \Exception implements ApiClientExceptionInterface
{
    private array $extraAttributes;

    public function __construct(array $extraAttributes)
    {
        parent::__construct(sprintf('The extra query options are not allowed : %s', implode(', ', $extraAttributes)));

        $this->extraAttributes = $extraAttributes;
    }

    public function getExtraAttributes(): array
    {
        return $this->extraAttributes;
    }
}
