<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

final readonly class GenericBiome implements Biome
{
    public function getTileColor(Tile $tile): string
    {
        return 'pink';
    }
}
