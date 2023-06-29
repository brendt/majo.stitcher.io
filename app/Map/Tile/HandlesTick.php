<?php

namespace App\Map\Tile;

use App\Map\Actions\Action;
use App\Map\MapGame;

interface HandlesTick
{
    public function handleTick(MapGame $game): Action;
}
