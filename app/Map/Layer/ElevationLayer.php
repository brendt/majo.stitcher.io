<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;

final readonly class ElevationLayer implements Layer
{
    public function __construct(
        private Noise $generator
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        $elevation = $this->generator->generate(
            $tile->x,
            $tile->y,
        );

//        $elevation = ($elevation / 2) + .5;

        return $tile
            ->setElevation($elevation)
            ->setTemperature($elevation);
    }
}
