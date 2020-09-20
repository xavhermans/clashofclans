<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\Paginator;

use Fivem\ClashOfClans\Model\Clan\Clan;
use Fivem\ClashOfClans\Model\Paging;

class SearchClansPaginator
{
    /** @var Clan[] */
    public array $items;
    public Paging $paging;
}
