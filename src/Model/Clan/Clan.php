<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\Clan;

use Fivem\ClashOfClans\Model\BadgeUrls;
use Fivem\ClashOfClans\Model\Label;
use Fivem\ClashOfClans\Model\Location;
use Fivem\ClashOfClans\Model\Model;

class Clan implements Model
{
    public string $tag;
    public string $name;
    public string $type;
    public string $description;
    public Location $location;
    public BadgeUrls $badgeUrls;
    public int $clanLevel;
    public int $clanPoints;
    public int $clanVersusPoints;
    public int $requiredTrophies;
    public string $warFrequency;
    public int $warWinStreak;
    public int $warWins;
    public int $warTies;
    public int $warLosses;
    public bool $isWarLogPublic;
    public WarLeague $warLeague;
    public int $members;
    /** @var Label[] */
    public array $labels;
    /** @var ClanMember[] */
    public array $memberList;
}
