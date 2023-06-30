<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final class BeachBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        $elevation = $tile->getElevation() ** 0.2;

        $r = hex($elevation);
        $g = hex($elevation);
        $b = hex($elevation / 2);

        return "#{$r}{$g}{$b}";
    }
}
