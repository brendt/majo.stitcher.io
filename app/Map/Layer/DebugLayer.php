<?php

namespace App\Map\Layer;

use App\Map\Noise\BasicNoise;
use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\GenericTile\DebugTile;
use App\Map\Tile\Tile;

final readonly class DebugLayer implements Layer
{
    public function __construct(
        private Noise $basicNoise,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        return new DebugTile(
            x: $tile->getX(),
            y: $tile->getY(),
            noise: $this->basicNoise->generate($tile->getX(), $tile->getY()),
//            noise: $tile->getPoint()->hash(1),
        );
    }
}
