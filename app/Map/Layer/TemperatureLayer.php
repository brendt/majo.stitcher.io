<?php

namespace App\Map\Layer;

use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;

final readonly class TemperatureLayer
{
    public function __construct() {}

    public function __invoke(Tile $tile): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        return $tile->setTemperature($tile->elevation);
    }
}
