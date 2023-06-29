<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

final readonly class DebugBiome implements Biome
{
    public function getTileColor(BaseTile $tile): string
    {
        return 'pink';
    }
}
