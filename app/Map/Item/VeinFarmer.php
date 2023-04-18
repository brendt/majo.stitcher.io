<?php

namespace App\Map\Item;

use App\Map\MapGame;
use App\Map\Tile\ResourceTile\GoldVeinTile;
use App\Map\Tile\ResourceTile\StoneVeinTile;
use App\Map\Tile\Tile;

final class VeinFarmer implements Item
{
    public function canInteract(Tile $tile): bool
    {
        return $tile instanceof GoldVeinTile
            || $tile instanceof StoneVeinTile;
    }

    public function handleTicks(MapGame $game, Tile $tile, int $ticks): void
    {
        if ($tile instanceof GoldVeinTile) {
            $game->goldCount += $ticks;
        } elseif ($tile instanceof StoneVeinTile) {
            $game->stoneCount += $ticks;
        }
    }
}
