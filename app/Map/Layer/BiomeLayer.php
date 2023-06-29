<?php

namespace App\Map\Layer;

use App\Map\Biome\BeachBiome;
use App\Map\Biome\ForestBiome;
use App\Map\Biome\MountainBiome;
use App\Map\Biome\PlainsBiome;
use App\Map\Biome\SeaBiome;
use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\Tile;

final readonly class BiomeLayer
{
    public function __construct(
        private Noise $generator
    ) {}

    public function __invoke(Tile $tile, BaseLayer $base): Tile
    {
        $elevation = $this->elevation(
            tile: $tile,
            base: $base
        );

        $biome = match (true) {
            $elevation < 0.4 => new SeaBiome(),
            $elevation >= 0.4 && $elevation < 0.44 => new BeachBiome(),
            $elevation >= 0.44 && $elevation < 0.6 => new PlainsBiome(),
            $elevation >= 0.6 && $elevation < 0.8 => new ForestBiome(),
            $elevation >= 0.8 => new MountainBiome(),
        };

        return $elevation < 0.4
            ? new WaterTile(
                point: $tile->getPoint(),
                elevation: $elevation,
                biome: $biome,
            )
            : new LandTile(
                point: $tile->getPoint(),
                elevation: $elevation,
                biome: $biome,
            );
    }

    private function elevation(Tile $tile, BaseLayer $base): float
    {
        $currentElevation = $this->generator->generate($tile->getPoint());

//        return $currentElevation;

        $middleX = $base->width / 2;
        $middleY = $base->height / 2;

        $distanceFromMiddle = sqrt(
            pow(($tile->getPoint()->x - $middleX), 2)
            + pow(($tile->getPoint()->y - $middleY), 2)
        );

        $maxDistanceFromMiddle = sqrt(
            pow(($base->width - $middleX), 2)
            + pow(($base->height - $middleY), 2)
        );

        $newElevation = 1 - ($distanceFromMiddle / $maxDistanceFromMiddle) + 0.2;

        return $currentElevation * $newElevation;
    }
}
