<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final class ForestBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        $g = hex((1 - $tile->getElevation()) ** 0.8);

        return "#00{$g}00";
    }
}
