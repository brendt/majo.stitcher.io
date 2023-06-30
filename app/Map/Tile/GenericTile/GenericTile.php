<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Biome\GenericBiome;
use App\Map\Point;
use App\Map\Tile\Tile;
use App\Map\Tile\Traits\BaseTileTrait;

final readonly class GenericTile implements Tile
{
    use BaseTileTrait;

    public function __construct(
        public Point $point,
        public float $elevation = 0.0,
        public Biome $biome = new GenericBiome(),
    ) {}
}
