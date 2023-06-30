<?php

namespace App\Map\Biome;

use App\Map\Tile\Tile;

interface Biome
{
    public function getTileColor(Tile $tile): string;
}
