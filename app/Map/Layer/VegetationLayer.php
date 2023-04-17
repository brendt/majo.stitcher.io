<?php

namespace App\Map\Layer;

use App\Map\Biome\DesertBiome;
use App\Map\Biome\ForestBiome;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\CactusTile;
use App\Map\Tile\DebugTile;
use App\Map\Tile\Tile;
use App\Map\Tile\TreeTile;

final readonly class VegetationLayer implements Layer
{
    public function __construct(
        private PerlinGenerator $generator,
    ) {}


    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if ($tile->getBiome()::class === ForestBiome::class) {
            $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

            if ($noise < 0) {
                return $tile;
            }

            return new TreeTile($noise);
        }

        if ($tile->getBiome()::class === DesertBiome::class) {
            $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

            if ($noise < 0.3 || $noise > 0.5) {
                return $tile;
            }

            return new CactusTile($noise);
        }

        return $tile;
    }
}
