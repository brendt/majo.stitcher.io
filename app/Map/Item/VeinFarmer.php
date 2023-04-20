<?php

namespace App\Map\Item;

use App\Map\MapGame;
use App\Map\Tile\ResourceTile\GoldVeinTile;
use App\Map\Tile\ResourceTile\StoneVeinTile;
use App\Map\Tile\Tile;

final class VeinFarmer implements TileItem
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

    public function getPrice(): ItemPrice
    {
        return new ItemPrice(
            wood: 50,
            gold: 20,
            stone: 20,
        );
    }

    public function getName(): string
    {
        return 'Vein Farmer';
    }

    public function getId(): string
    {
        return 'VeinFarmer';
    }
}
