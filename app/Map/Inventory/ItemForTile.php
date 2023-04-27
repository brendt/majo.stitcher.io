<?php

namespace App\Map\Inventory;

use App\Map\MapGame;
use App\Map\Tile\Tile;

interface ItemForTile extends Item
{
    public function canBeUsedOn(Tile $tile, MapGame $game): bool;

    public function useOn(Tile $tile, MapGame $game): void;
}
