<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Query;

class SearchClansQuery extends BaseQuery
{
    public string $name;
    public string $warFrequency;
    public int $locationId;
    public int $minMembers;
    public int $maxMembers;
    public int $minClanPoints;
    public int $minClanLevel;
    public int $limit;
    public string $after;
    public string $before;
    public string $labelIds;
}
