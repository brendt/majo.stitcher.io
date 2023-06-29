<?php

namespace App\Map\Tile;

use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Tile\Style\Style;

interface Tile extends HasTooltip, HasStyle
{
    public function getPoint(): Point;

    public function getBiome(): Biome;

    public function toArray(MapGame $game): array;
}
