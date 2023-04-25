<?php

namespace App\Http\Controllers;

use App\Map\MapGame;

final class MapController
{
    public function __invoke(?int $seed = null)
    {
        $game = MapGame::resolve();

        return view('map', [
            'seed' => $seed ?? time(),
            'board' => $game
                ->baseLayer
                ->generate()
                ->getBoard(),
            'game' => $game,
        ]);
    }
}
