<?php

namespace App\Map\Item;

use App\Map\MapGame;
use App\Map\Tile\Tile;
use App\Map\Tile\ResourceTile\TreeTile;

final class TreeFarmer implements TileItem
{
    public function canInteract(Tile $tile): bool
    {
        return $tile instanceof TreeTile;
    }

    public function handleTicks(MapGame $game, Tile $tile, int $ticks): void
    {
        $game->woodCount += $ticks;
    }

    public function getPrice(): ItemPrice
    {
        return new ItemPrice(
            wood: 20,
            stone: 20,
        );
    }

    public function getName(): string
    {
        return 'Tree Farmer';
    }

    public function getId(): string
    {
        return 'TreeFarmer';
    }
}
