<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

final class PlainsBiome implements Biome
{
    public function getTileColor(BaseTile $tile): string
    {
        $g = hex($tile->elevation);
        $b = hex($tile->elevation / 4);

        return "#00{$g}{$b}";
    }
}
