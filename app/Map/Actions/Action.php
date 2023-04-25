<?php

namespace App\Map\Actions;

use App\Map\MapGame;

interface Action
{
    public function __invoke(MapGame $game);
}
