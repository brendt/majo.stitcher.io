<?php

namespace App\Map\Item\HandHeldItem;

use App\Map\Item\HandHeldItem;
use App\Map\Price;
use App\Map\MapGame;
use App\Map\Tile\ResourceTile\WoodTile;
use App\Map\Tile\Tile;

final class Axe implements HandHeldItem
{
    public function getId(): string
    {
        return 'Axe';
    }

    public function getName(): string
    {
        return 'Axe';
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
        return $tile instanceof WoodTile;
    }

    public function getModifier(): int
    {
        return 2;
    }
}
