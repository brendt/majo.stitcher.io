<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final class DesertBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        $r = hex($tile->getElevation() / 1.5);
        $g = hex($tile->getElevation());

        return "#{$r}{$g}00";
    }
}
