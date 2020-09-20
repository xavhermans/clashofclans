<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\CurrentWar;

use Fivem\ClashOfClans\Model\Model;

class CurrentWarAttack implements Model
{
    public string $attackerTag;
    public string $defenderTag;
    public int $stars;
    public int $destructionPercentage;
    public int $order;
}
