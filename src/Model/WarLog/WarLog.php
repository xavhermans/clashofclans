<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\WarLog;

use Fivem\ClashOfClans\Model\Model;

class WarLog implements Model
{
    public ?string $result;
    public string $endTime;
    public int $teamSize;
    public WarLogClan $clan;
    public WarLogClan $opponent;
}
