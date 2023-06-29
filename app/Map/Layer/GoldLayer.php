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

final readonly class GoldLayer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function __invoke(LandTile $tile): Tile
    {
        if ($tile->getBiome()::class === MountainBiome::class) {
            $noise = $this->noise->amount(0.09)->generate($tile->getPoint());

            if ($noise <= 0.0) {
                return $tile;
            }

            return new GoldTile(
                point: $tile->point,
                elevation: $noise,
                biome: $tile->biome,
            );
        }

        return $tile;
    }
}
