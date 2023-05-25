<?php

namespace App\Map\Layer;

use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;

final readonly class TemperatureLayer implements Layer
{
    public function __construct() {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        return $tile->setTemperature($tile->elevation);
    }
}
