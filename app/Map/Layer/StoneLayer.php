<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\StoneTile;
use App\Map\Tile\Tile;

final readonly class StoneLayer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function __invoke(LandTile $tile): Tile
    {
        $noise = $this->noise->amount(0.07)->generate($tile->getPoint());

        if ($noise <= 0.0) {
            return $tile;
        }

        return new StoneTile(
            point: $tile->point,
            elevation: $noise,
            biome: $tile->biome,
        );
    }
}
