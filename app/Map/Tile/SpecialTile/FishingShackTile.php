<?php

namespace App\Map\Tile\SpecialTile;

use App\Map\MapGame;
use App\Map\Price;
use App\Map\Tile\HasBorder;
use App\Map\Tile\Purchasable;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Tile;
use App\Map\Tile\Traits\BaseTileTrait;

final class FishingShackTile implements Tile, Purchasable, HasBorder
{
    use BaseTileTrait;

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle(color: 'brown', width: 5);
    }

    public function getName(): string
    {
        return 'FishingShackTile';
    }

    public function getPrice(MapGame $game): Price
    {
        return new Price(wood: 1);
    }
}
