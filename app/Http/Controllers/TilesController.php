<?php

namespace App\Http\Controllers;

use App\Map\MapGame;
use App\Map\Tile\Tile;
use Illuminate\Http\Request;

class TilesController
{
    public function __invoke(Request $request)
    {
        $game = MapGame::resolve();

        $tiles = $request->query->has('all')
            ? $game->getAllTiles()
            : $game->getOwnTiles();

        return [
            'tiles' => collect($tiles)
                ->flatten()
                ->map(fn (Tile $tile) => $tile->toArray($game))
                ->toArray(),
            'game' => $game->toArray(),
        ];
    }
}
