<?php

namespace App\Map\Item;

use App\Map\Tile\ResourceTile\FishTile;
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

    public function getPrice(): ItemPrice
    {
        return new ItemPrice(
            wood: 20,
            flax: 20,
        );
    }

    public function canInteract(Tile $tile): bool
    {
        return $tile instanceof FishTile;
    }

    public function getModifier(): int
    {
        return 2;
    }
}
