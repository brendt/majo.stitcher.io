<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\ResourceTile\FishTile;
use App\Map\Tile\Tile;

final readonly class FishLayer implements Layer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof WaterTile) {
            return $tile;
        }

        $noise = $this->noise->amount(0.02)->generate($tile->x, $tile->y);

        if ($noise <= 0.0) {
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
