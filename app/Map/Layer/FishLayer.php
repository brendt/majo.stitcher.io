<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\ResourceTile\FishTile;
use App\Map\Tile\Tile;

final readonly class FishLayer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function __invoke(WaterTile $tile, BaseLayer $base): Tile
    {
        $noise = $this->noise->amount(0.02)->generate($tile->getPoint());

        if ($noise <= 0.0) {
            return $tile;
        }

        return new FishTile(
            point: $tile->point,
            elevation: $tile->elevation,
            biome: $tile->biome,
        );
    }
}
