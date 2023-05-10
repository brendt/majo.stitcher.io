<?php

namespace App\Map\Layer;

use App\Map\Biome\ForestBiome;
use App\Map\Biome\PlainsBiome;
use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\WoodTile;
use App\Map\Tile\Tile;

final readonly class WoodLayer implements Layer
{
    public function __construct(
        private Noise $generator,
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

        return $tile;
    }

    private function forestTile(Tile $tile): Tile
    {
        $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

        if ($noise < 0) {
            return $tile;
        }

        return new WoodTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: $noise,
        );
    }

    private function plainsTile(Tile $tile): Tile
    {
        $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

        if ($noise < 0.7 || $noise > 0.8) {
            return $tile;
        }

        return new WoodTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: $noise,
        );
    }
}
