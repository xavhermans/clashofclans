<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Model;

class Paging implements Model
{
    public int $id;
    public array $cursors;
}
