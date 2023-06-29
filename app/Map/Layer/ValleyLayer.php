<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;

final readonly class ValleyLayer
{
    public function __construct(private Noise $noise) {}

    public function __invoke(Tile $tile, BaseLayer $base): Tile
    {
        if (! $tile instanceof BaseTile) {
            return $tile;
        }

        $middleX = $base->width / 2;
        $middleY = $base->height / 2;

        $distanceFromMiddle = sqrt(
            pow(($tile->getPoint()->x - $middleX), 2)
            + pow(($tile->getPoint()->y - $middleY), 2)
        );

        $maxDistanceFromMiddle = sqrt(
            pow(($base->width - $middleX), 2)
            + pow(($base->height - $middleY), 2)
        );

        $newElevation = ($distanceFromMiddle / $maxDistanceFromMiddle) + 0.5;

        return $tile->setElevation($tile->elevation * $newElevation);
    }
}
