<?php

namespace App\Map\Layer;

use App\Map\Biome\ForestBiome;
use App\Map\Biome\MountainBiome;
use App\Map\Biome\PlainsBiome;
use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\WoodTile;
use App\Map\Tile\Tile;

final readonly class WoodLayer implements Layer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof LandTile) {
            return $tile;
        }

        $biome = $tile->getBiome();

        if ($biome instanceof ForestBiome) {
            return $this->forestTile($tile);
        }

        if ($biome instanceof PlainsBiome) {
            return $this->plainsTile($tile);
        }

        if ($biome instanceof MountainBiome) {
            return $this->mountainTile($tile);
        }

        return $tile;
    }

    private function forestTile(BaseTile $tile): Tile
    {
        $noise = $this->noise->amount(0.1)->generate($tile->x, $tile->y);

        if ($noise <= 0.0) {
            return $tile;
        }

        return new WoodTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: $tile->elevation * $noise,
        );
    }

    private function plainsTile(Tile $tile): Tile
    {
        $noise = $this->noise->amount(0.006)->generate($tile->x, $tile->y);

        if ($noise <= 0.0) {
            return $tile;
        }

        return new WoodTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: $tile->elevation * $noise,
        );
    }

    private function mountainTile(BaseTile $tile): Tile
    {
        $noise = $this->noise->amount(0.2)->generate($tile->x, $tile->y);

        if ($noise <= 0.0) {
            return $tile;
        }

        if ($tile->elevation > 0.85) {
            return $tile;
        }

        return new WoodTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: 1 - $tile->elevation * $noise,
        );
    }
}
