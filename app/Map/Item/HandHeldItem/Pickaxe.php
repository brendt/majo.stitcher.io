<?php

namespace App\Map\Item\HandHeldItem;

use App\Map\Item\HandHeldItem;
use App\Map\MapGame;
use App\Map\Price;
use App\Map\Tile\ResourceTile\old\GoldVeinTile;
use App\Map\Tile\ResourceTile\old\StoneVeinTile;
use App\Map\Tile\Tile;

final class Pickaxe implements HandHeldItem
{
    public function getId(): string
    {
        return 'Pickaxe';
    }

    public function getName(): string
    {
        return 'Pickaxe';
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
        return $tile instanceof StoneVeinTile
            || $tile instanceof GoldVeinTile;
    }

    public function getModifier(): int
    {
        return 2;
    }
}
