<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\Clan;

use Fivem\ClashOfClans\Model\League;
use Fivem\ClashOfClans\Model\Model;

class ClanMember implements Model
{
    public int $id;
    public string $tag;
    public string $name;
    public string $role;
    public int $expLevel;
    public League $league;
    public int $trophies;
    public int $versusTrophies;
    public int $clanRank;
    public int $previousClanRank;
    public int $donations;
    public int $donationsReceived;
}
