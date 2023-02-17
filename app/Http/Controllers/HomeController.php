<?php

namespace App\Http\Controllers;

use App\Http\Livewire\StandardBoard;

final class HomeController
{
    public function __invoke(?string $seed = null)
    {
        if (! is_numeric($seed)) {
            $seed = (int) bin2hex($seed);
        }

        $board = StandardBoard::resolve();

        if ($seed && $board->seed !== $seed) {
            $board->reset($seed);
        }

        return view('home', [
            'seed' => $seed,
        ]);
    }
}
