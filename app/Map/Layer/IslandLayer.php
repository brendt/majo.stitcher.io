<?php

namespace App\Map\Layer;

use App\Map\Tile\Tile;

final readonly class IslandLayer
{
    public function __invoke(Tile $tile, BaseLayer $base): Tile
    {
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

        $newElevation = 1 - ($distanceFromMiddle / $maxDistanceFromMiddle) + 0.2;

        return $tile->setElevation($tile->elevation * $newElevation);
    }
}
