<?php

namespace App\Map\Layer;

use App\Map\Biome\BeachBiome;
use App\Map\Biome\Biome;
use App\Map\Biome\DesertBiome;
use App\Map\Biome\ForestBiome;
use App\Map\Biome\MountainBiome;
use App\Map\Biome\PlainsBiome;
use App\Map\Biome\SeaBiome;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\Tile;

final readonly class BiomeLayer implements Layer
{
    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        $biome = match (true) {
            $tile->elevation < 0.4 => new SeaBiome(),
            $tile->elevation >= 0.4 && $tile->elevation < 0.44 => new BeachBiome(),
            $tile->elevation >= 0.44 && $tile->elevation < 0.6 => new PlainsBiome(),
            $tile->elevation >= 0.6 && $tile->elevation < 0.8 => new ForestBiome(),
            $tile->elevation >= 0.8 => new MountainBiome(),
        };

        $tile = $tile->setBiome($biome);

        return $tile->elevation < 0.4
            ? WaterTile::fromBase($tile)
            : LandTile::fromBase($tile);
    }
}
