<?php

namespace App\Http\Controllers;

use App\Map\MapGame;

class MapController
{
    public function __invoke(?int $seed = null)
    {
        $game = MapGame::resolve($seed);

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
