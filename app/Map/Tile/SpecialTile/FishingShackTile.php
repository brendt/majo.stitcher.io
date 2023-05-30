<?php

namespace App\Map\Tile\SpecialTile;

use App\Map\MapGame;
use App\Map\Price;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HasBorder;
use App\Map\Tile\Purchasable;

final class FishingShackTile extends BaseTile implements Purchasable, HasBorder
{
    public function getColor(): string
    {
        return 'olive';
    }

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
