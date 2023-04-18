<?php

namespace App\Map\Item;

use App\Map\MapGame;
use App\Map\Tile\Tile;

interface Item
{
    public function canInteract(Tile $tile): bool;

    public function handleTicks(MapGame $game, Tile $tile, int $ticks): void;
}
