<?php

namespace App\Http\Controllers;

use App\Map\MapGame;

class ResetMapController
{
    public function __invoke()
    {
        MapGame::resolve()->destroy();

        return redirect()->action(MapController::class);
    }
}
