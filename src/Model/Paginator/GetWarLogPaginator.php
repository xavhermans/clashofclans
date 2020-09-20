<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\Paginator;

use Fivem\ClashOfClans\Model\Paging;
use Fivem\ClashOfClans\Model\WarLog\WarLog;

class GetWarLogPaginator
{
    /** @var WarLog[] */
    public array $items;
    public Paging $paging;
}
