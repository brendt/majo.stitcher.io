<?php

namespace App\Http\Controllers;

use App\Game\Board;

class HomeController extends Controller
{
    public function __invoke(?string $seed = null)
    {
        $board = Board::resolve();

        if (($board->seed ?? null) !== $seed) {
            $board->destroy();
        }

        return view('home', [
            'seed' => $seed,
        ]);
    }
}
