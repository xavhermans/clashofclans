<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\WarLog;

use Fivem\ClashOfClans\Model\BadgeUrls;
use Fivem\ClashOfClans\Model\Model;

class WarLogClan implements Model
{
    public string $name;
    public string $tag;
    public BadgeUrls $badgeUrls;
    public int $clanLevel;
    public int $attacks;
    public int $stars;
    public float $destructionPercentage;
    public int $expEarned;
}
