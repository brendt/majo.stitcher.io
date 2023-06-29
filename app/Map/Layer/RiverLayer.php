<?php

namespace App\Map\Layer;

use App\Map\Biome\ForestBiome;
use App\Map\Biome\SeaBiome;
use App\Map\Noise\BasicNoise;
use App\Map\Noise\Noise;
use App\Map\Noise\PerlinGenerator;
use App\Map\Noise\PerlinNoise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\GenericTile\DebugTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\Tile;

final readonly class RiverLayer
{
    public function __construct(private int $seed) {}


    public function __invoke(Tile $tile): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        if ($tile->getBiome() instanceof SeaBiome) {
            return $tile;
        }

        if ($tile->getBiome() instanceof ForestBiome) {
            return $tile;
        }

        $noise = new PerlinGenerator($this->seed);

        $noise = abs($noise->generate($tile->getPoint()));

        if ($noise > 0.2 || $noise < 0.1) {
            return $tile;
        }

//        return new DebugTile($tile->getPoint()->x, $tile->$this->getPoint()->y, $noise);

        return new WaterTile(
            $tile->point,
        );
    }
}
