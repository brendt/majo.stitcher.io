<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final class SeaBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        $elevation = $tile->getElevation();

        while ($elevation < 0.25) {
            $elevation += 0.01;
        }

        $r = hex($elevation / 3);
        $g = hex($elevation / 3);
        $b = hex($elevation);

        return "#{$r}{$g}{$b}";
    }
}
