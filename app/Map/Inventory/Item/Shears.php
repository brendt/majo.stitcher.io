<?php

namespace App\Map\Inventory\Item;

use App\Map\Inventory\FlaxTile;
use App\Map\Inventory\HandHeldItem;
use App\Map\MapGame;
use App\Map\Price;
use App\Map\Tile\Tile;

final class Shears implements HandHeldItem
{
    public function getId(): string
    {
        return 'Shears';
    }

    public function getName(): string
    {
        return 'Shears';
    }

    public function getPrice(): Price
    {
        return new Price(
            wood: 20,
            stone: 20,
        );
    }

    public function canInteract(MapGame $game, Tile $tile): bool
    {
        return $tile instanceof FlaxTile;
    }

    public function getModifier(): int
    {
        return 2;
    }
}