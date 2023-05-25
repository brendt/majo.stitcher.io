<?php

namespace App\Map\Layer;

use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\Tile;
use App\Map\Tile\GenericTile\WaterTile;

final readonly class LandLayer implements Layer
{
    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        return $tile->elevation <= 0.4
            ? WaterTile::fromBase($tile)
            : LandTile::fromBase($tile);
    }
}
