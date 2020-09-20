<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\CurrentWar;

use Fivem\ClashOfClans\Model\BadgeUrls;
use Fivem\ClashOfClans\Model\Model;

class CurrentWarClan implements Model
{
    public string $name;
    public string $tag;
    public BadgeUrls $badgeUrls;
    public string $type;
    public int $clanLevel;
    public int $attacks;
    public int $stars;
    public float $destructionPercentage;
    /** @var CurrentWarMember[] */
    public array $members;
}
