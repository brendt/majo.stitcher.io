<?php

namespace App\Map\Tile;

use App\Map\HasName;
use App\Map\MapGame;
use App\Map\Price;

interface Purchasable extends HasName
{
    public function getPrice(MapGame $game): Price;
}
