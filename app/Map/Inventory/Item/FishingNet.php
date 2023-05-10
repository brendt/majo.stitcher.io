<?php

namespace App\Map\Inventory\Item;

use App\Map\Inventory\HandHeldItem;
use App\Map\MapGame;
use App\Map\Price;
use App\Map\Tile\ResourceTile\old\FishTile;
use App\Map\Tile\Tile;

final class FishingNet implements HandHeldItem
{
    public function getId(): string
    {
        return 'FishingNet';
    }

    public function getName(): string
    {
        return 'Fishing Net';
    }

    public function getPrice(): Price
    {
        return new Price(
            wood: 20,
            flax: 20,
        );
    }

    public function canInteract(MapGame $game, Tile $tile): bool
    {
        return $tile instanceof FishTile;
    }

    public function getModifier(): int
    {
        return 2;
    }
}