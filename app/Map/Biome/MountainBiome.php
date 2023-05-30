<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

final class MountainBiome implements Biome
{
    public function getTileColor(BaseTile $tile): string
    {
        if ($tile->elevation >= 0.95) {
            $r = hex($tile->elevation ** 4);
        } else {
            $r = hex($tile->elevation / 2);
        }

        return "#{$r}{$r}{$r}";
    }
}
