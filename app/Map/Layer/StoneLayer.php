<?php

namespace App\Map\Layer;

use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\StoneTile;
use App\Map\Tile\Tile;

final readonly class StoneLayer implements Layer
{
    public function __construct(
        private PerlinGenerator $generator,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof LandTile) {
            return $tile;
        }

        $noise = $this->generator->noise($tile->x, $tile->y, 0, 3);

        if ($noise < 0.5 || $noise > 0.52) {
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
