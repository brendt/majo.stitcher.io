<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

final class DesertBiome implements Biome
{
    public function getTileColor(BaseTile $tile): string
    {
        $r = hex($tile->elevation / 1.5);
        $g = hex($tile->elevation);

        return "#{$r}{$g}00";
    }
}
