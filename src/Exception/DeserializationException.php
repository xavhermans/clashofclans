<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Exception;

class DeserializationException extends \Exception implements ApiClientExceptionInterface
{
    private string $modelClassName;
    private string $property;
    private $value;

    public function __construct(string $modelClassName, string $property, $value)
    {
        parent::__construct(sprintf(
            'Could not deserialize property "%s" on model %s (value type = "%s")',
            $property,
            $modelClassName,
            \gettype($value)
        ));

        $this->modelClassName = $modelClassName;
        $this->property = $property;
        $this->value = $value;
    }

    public function getModelClassName(): string
    {
        return $this->modelClassName;
    }

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getValue()
    {
        return $this->value;
    }
}
