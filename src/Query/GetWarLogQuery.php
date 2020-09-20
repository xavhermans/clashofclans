<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Query;

class GetWarLogQuery extends BaseQuery
{
    public string $clanTag;
    public int $limit;
    public string $after;
    public string $before;
}
