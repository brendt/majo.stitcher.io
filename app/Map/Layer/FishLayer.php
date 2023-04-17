<?php

namespace App\Map\Layer;

use App\Map\Biome\DesertBiome;
use App\Map\Biome\ForestBiome;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\CactusTile;
use App\Map\Tile\DebugTile;
use App\Map\Tile\FishTile;
use App\Map\Tile\Tile;
use App\Map\Tile\TreeTile;
use App\Map\Tile\WaterTile;

final readonly class FishLayer implements Layer
{
    public function __construct(
        private PerlinGenerator $generator,
    ) {}


    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof WaterTile) {
            return $tile;
        }

        $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

        if ($noise < 0.3 || $noise > 0.32) {
            return $tile;
        }

        return new FishTile($noise);
    }
}
