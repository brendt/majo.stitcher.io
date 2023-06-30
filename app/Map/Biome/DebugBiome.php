<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final readonly class DebugBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        return 'pink';
    }
}
