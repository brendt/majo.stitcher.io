<?php

namespace App\Map\Layer;

use App\Map\Biome\PlainsBiome;
use App\Map\Noise\Noise;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\FlaxTile;
use App\Map\Tile\Tile;

final readonly class FlaxLayer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function __invoke(LandTile $tile): Tile
    {
        if ($tile->getBiome()::class !== PlainsBiome::class) {
            return $tile;
        }

        $noise = $this->noise->amount(0.05)->generate($tile->getPoint());

        if ($noise <= 0.0) {
            return $tile;
        }

        return new FlaxTile(
            point: $tile->point,
            elevation: $noise,
            biome: $tile->biome,
        );
    }
}
