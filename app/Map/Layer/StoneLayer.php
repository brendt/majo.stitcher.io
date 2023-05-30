<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\StoneTile;
use App\Map\Tile\Tile;

final readonly class StoneLayer implements Layer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof LandTile) {
            return $tile;
        }

        $noise = $this->noise->amount(0.06)->generate($tile->x, $tile->y);

        if ($noise <= 0.0) {
            return $tile;
        }

        return new StoneTile(
            x: $tile->x,
            y: $tile->y,
            temperature: $tile->temperature,
            elevation: $tile->elevation,
            biome: $tile->biome,
            noise: $noise
        );
    }
}
