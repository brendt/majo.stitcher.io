<?php

namespace App\Map\Item;

use App\Map\Tile\ResourceTile\TreeTile;
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

    public function getPrice(): ItemPrice
    {
        return new ItemPrice(
            wood: 20,
            stone: 20,
        );
    }

    public function canInteract(Tile $tile): bool
    {
        return $tile instanceof TreeTile;
    }

    public function getModifier(): int
    {
        return 2;
    }
}
