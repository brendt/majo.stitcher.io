<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

final class BeachBiome implements Biome
{
    public function getTileColor(BaseTile $tile): string
    {
        $elevation = $tile->elevation ** 0.2;

        $r = hex($elevation);
        $g = hex($elevation);
        $b = hex($elevation / 2);

        return "#{$r}{$g}{$b}";
    }
}
