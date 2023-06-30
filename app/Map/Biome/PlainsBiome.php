<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final class PlainsBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        $g = hex($tile->getElevation());
        $b = hex($tile->getElevation() / 4);

        return "#00{$g}{$b}";
    }
}
