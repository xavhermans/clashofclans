<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model;

class Location implements Model
{
    public int $id;
    public string $name;
    public bool $isCountry;
    public string $countryCode;
    public string $localizedName;
}
