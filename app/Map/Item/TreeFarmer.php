<?php

namespace App\Map\Item;

use App\Map\MapGame;
use App\Map\Tile\Tile;
use App\Map\Tile\ResourceTile\TreeTile;

final class TreeFarmer implements Item
{
    public function canInteract(Tile $tile): bool
    {
        return $tile instanceof TreeTile;
    }

    public function handleTicks(MapGame $game, Tile $tile, int $ticks): void
    {
        $game->woodCount += $ticks;
    }
}
