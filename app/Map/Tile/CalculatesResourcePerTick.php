<?php

namespace App\Map\Tile;

use App\Map\Actions\Action;
use App\Map\MapGame;
use App\Map\Tile\ResourceTile\Resource;

interface CalculatesResourcePerTick
{
    public function getResourcePerTick(MapGame $game, Resource $resource): int;
}
