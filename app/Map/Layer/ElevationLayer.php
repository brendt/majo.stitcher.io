<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;

final readonly class ElevationLayer
{
    public function __construct(
        private Noise $generator
    ) {}

    public function __invoke(BaseTile $tile): Tile
    {
        $elevation = $this->generator->generate($tile->getPoint());

        return $tile
            ->setElevation($elevation)
            ->setTemperature($elevation);
    }
}
