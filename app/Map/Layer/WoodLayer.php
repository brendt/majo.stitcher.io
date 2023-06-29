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

final readonly class WoodLayer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function __invoke(LandTile $tile): Tile
    {
        $biome = $tile->getBiome();

        if ($biome instanceof ForestBiome) {
            return $this->forestTile($tile);
        }

        if ($biome instanceof PlainsBiome) {
            return $this->plainsTile($tile);
        }

        if ($biome instanceof MountainBiome) {
//            return $this->mountainTile($tile);
        }

        return $tile;
    }

    private function forestTile(LandTile $tile): Tile
    {
        $noise = $this->noise->amount(0.1)->generate($tile->getPoint());

        if ($noise <= 0.0) {
            return $tile;
        }

        return new WoodTile(
            point: $tile->point,
            elevation: $tile->elevation * $noise,
            biome: $tile->biome,
        );
    }

    private function plainsTile(LandTile $tile): Tile
    {
        $noise = $this->noise->amount(0.006)->generate($tile->getPoint());

        if ($noise <= 0.0) {
            return $tile;
        }

        return new WoodTile(
            point: $tile->point,
            elevation: $tile->elevation * $noise,
            biome: $tile->biome,
        );
    }

    private function mountainTile(LandTile $tile): Tile
    {
        $noise = $this->noise->amount(0.2)->generate($tile->getPoint());

        if ($noise <= 0.0) {
            return $tile;
        }

        if ($tile->elevation > 0.95) {
            return $tile;
        }

        return new WoodTile(
            point: $tile->point,
            elevation: 1 - $tile->elevation * $noise,
            biome: $tile->biome,
        );
    }
}
