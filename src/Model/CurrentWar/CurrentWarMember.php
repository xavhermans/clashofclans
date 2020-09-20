<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model\CurrentWar;

use Fivem\ClashOfClans\Model\Model;

class CurrentWarMember implements Model
{
    public string $tag;
    public string $name;
    public int $townhallLevel;
    public int $mapPosition;
    /** @var CurrentWarAttack[] */
    public array $attacks;
    public int $opponentAttacks;
    public CurrentWarAttack $bestOpponentAttack;
}
