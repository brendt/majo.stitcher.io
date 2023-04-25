<?php

namespace App\Map\Layer;

use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\ResourceTile\old\FishTile;
use App\Map\Tile\Tile;

final readonly class FishLayer implements Layer
{
    public function __construct(
        private PerlinGenerator $generator,
    ) {}


    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof WaterTile) {
            return $tile;
        }

        $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

        if ($noise < 0.3 || $noise > 0.32) {
            return $tile;
        }

        return new FishTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: $noise,
        );
    }
}
