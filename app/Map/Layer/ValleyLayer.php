<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;

final readonly class ValleyLayer implements Layer
{
    public function __construct(private Noise $noise) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        $middleX = $base->width / 2;
        $middleY = $base->height / 2;

        $distanceFromMiddle = sqrt(
            pow(($tile->x - $middleX), 2)
            + pow(($tile->y - $middleY), 2)
        );

        $maxDistanceFromMiddle = sqrt(
            pow(($base->width - $middleX), 2)
            + pow(($base->height - $middleY), 2)
        );

        $newElevation = ($distanceFromMiddle / $maxDistanceFromMiddle) + 0.5;

        return $tile->setElevation($tile->elevation * $newElevation);
    }
}
