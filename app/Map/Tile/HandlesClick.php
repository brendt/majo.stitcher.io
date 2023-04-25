<?php

namespace App\Map\Tile;

use App\Map\Actions\Action;
use App\Map\MapGame;

interface HandlesClick
{
    public function handleClick(MapGame $game): Action;
}
