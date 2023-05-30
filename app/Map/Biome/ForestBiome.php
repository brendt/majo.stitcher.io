<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

final class ForestBiome implements Biome
{
    public function getTileColor(BaseTile $tile): string
    {
        $g = hex((1 - $tile->elevation) ** 0.8);

        return "#00{$g}00";
    }
}
