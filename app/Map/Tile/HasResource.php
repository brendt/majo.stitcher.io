<?php

namespace App\Map\Tile;

use App\Map\Item\TileItem;
use App\Map\Tile\ResourceTile\Resource;

interface HasResource
{
    public function getResource(): Resource;
}
