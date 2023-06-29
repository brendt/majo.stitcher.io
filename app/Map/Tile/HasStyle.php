<?php

namespace App\Map\Tile;

use App\Map\MapGame;
use App\Map\Tile\Style\Style;

interface HasStyle
{
    public function getStyle(MapGame $game): Style;
}
