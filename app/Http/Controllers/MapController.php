<?php

namespace App\Http\Controllers;

use App\Map\MapGame;
use Illuminate\Http\Request;

final class MapController
{
    public function __invoke(Request $request)
    {
        $game = MapGame::resolve();

        return view('map', [
            'seed' => $request->get('seed', time()),
            'board' => $game
                ->baseLayer
                ->generate()
                ->getBoard(),
            'game' => $game,
        ]);
    }
}
