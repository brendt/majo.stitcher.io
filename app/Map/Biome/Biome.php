<?php

namespace App\Map\Biome;

use App\Map\Tile\GenericTile\BaseTile;

interface Biome
{
    public function getTileColor(BaseTile $tile): string;
}
