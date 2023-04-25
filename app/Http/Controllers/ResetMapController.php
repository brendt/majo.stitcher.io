<?php

namespace App\Http\Controllers;

use App\Map\MapGame;

class ResetMapController
{
    public function __invoke()
    {
        MapGame::resolve()->destroy();

        MapGame::init(time())->persist();

        return redirect()->action(MapController::class);
    }
}
