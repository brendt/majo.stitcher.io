<?php

namespace App\Map\Layer;

use App\Map\Biome\DesertBiome;
use App\Map\Biome\IcePlainsBiome;
use App\Map\Biome\MesaBiome;
use App\Map\Biome\TundraBiome;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\ResourceTile\GoldTile;
use App\Map\Tile\Tile;

final readonly class GoldLayer implements Layer
{
    public function __construct(
        private PerlinGenerator $generator,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if ($tile instanceof WaterTile && $tile->getBiome()::class === IcePlainsBiome::class) {
            $noise = $this->generator->generate($tile->x, $tile->y, 3);

            if ($noise < 0.3 || $noise > 0.33) {
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

        if (! $tile instanceof LandTile) {
            return $tile;
        }

        if ($tile->getBiome()::class === MesaBiome::class) {
            $noise = $this->generator->generate($tile->x, $tile->y, 3);

            if ($noise < 0.3 || $noise > 0.35) {
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

        if (
            $tile->getBiome()::class === TundraBiome::class
            || $tile->getBiome()::class === DesertBiome::class
        ) {
            $noise = $this->generator->generate($tile->x, $tile->y, 3);

            if ($noise < 0.3 || $noise > 0.33) {
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
