<?php

namespace App\Http\Controllers;

use App\Map\MapGame;
use App\Map\Tile\Tile;

class TilesController
{
    public function __invoke()
    {
        $game = MapGame::resolve();

        return [
            'tiles' => collect($game->getOwnTiles())
                ->flatten()
                ->map(fn (Tile $tile) => $tile->toArray())
                ->toArray(),
        ];
    }
}
