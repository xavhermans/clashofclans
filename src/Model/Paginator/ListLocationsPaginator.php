<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\Paginator;

use Fivem\ClashOfClans\Model\Location;
use Fivem\ClashOfClans\Model\Paging;

class ListLocationsPaginator
{
    /** @var Location[] */
    public array $items;
    public Paging $paging;
}
