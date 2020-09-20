<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\CurrentWar;

use Fivem\ClashOfClans\Model\Model;

class CurrentWar implements Model
{
    public string $state;
    public int $teamSize;
    public string $preparationStartTime;
    public string $startTime;
    public string $endTime;
    public CurrentWarClan $clan;
    public CurrentWarClan $opponent;
}
