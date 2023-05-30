<?php

namespace App\Map\Layer;

use App\Map\Biome\DesertBiome;
use App\Map\Biome\IcePlainsBiome;
use App\Map\Biome\MountainBiome;
use App\Map\Biome\TundraBiome;
use App\Map\Noise\Noise;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\ResourceTile\GoldTile;
use App\Map\Tile\Tile;

final readonly class GoldLayer implements Layer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof LandTile) {
            return $tile;
        }

        if ($tile->getBiome()::class === MountainBiome::class) {
            $noise = $this->noise->amount(0.09)->generate($tile->x, $tile->y);

            if ($noise <= 0.0) {
                return $tile;
            }

            return new GoldTile(
                x: $tile->x,
                y: $tile->y,
                temperature: $tile->temperature,
                elevation: $tile->elevation,
                biome: $tile->biome,
                noise: $noise
            );
        }

        return $tile;
    }
}
