<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Query;

class ListLocationsQuery extends BaseQuery
{
    public int $limit;
    public string $after;
    public string $before;
}
