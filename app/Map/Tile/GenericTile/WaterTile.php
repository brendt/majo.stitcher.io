<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Point;
use App\Map\Tile\Tile;
use App\Map\Tile\Traits\BaseTileTrait;

final class WaterTile implements Tile
{
    use BaseTileTrait;

    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}
}
