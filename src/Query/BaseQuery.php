<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Query;

class BaseQuery implements Query
{
    private function __construct()
    {
    }

    public static function fromArray(array $options): self
    {
        $query = new static();
        foreach ($options as $optionName => $optionValue) {
            if (!property_exists(static::class, $optionName)) {
                throw new \InvalidArgumentException($optionName);
            }

            $query->$optionName = $optionValue;
        }

        return $query;
    }

    public function toArray(): array
    {
        $data = [];
        foreach ($this as $propertyName => $propertyValue) {
            $data[$propertyName] = $propertyValue;
        }

        return $data;
    }
}
