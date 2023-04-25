<?php

namespace App\Map\Actions;

use App\Map\MapGame;

final class DoNothing implements Action
{
    public function __invoke(MapGame $game): void {}
}
