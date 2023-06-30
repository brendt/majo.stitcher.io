<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final class MountainBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        if ($tile->getElevation() >= 0.95) {
            $r = hex($tile->getElevation() ** 4);
        } else {
            $r = hex($tile->getElevation() / 2);
        }

        return "#{$r}{$r}{$r}";
    }
}
