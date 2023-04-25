<?php

namespace App\Http\Controllers;

use App\Map\MapGame;
use Illuminate\Http\Request;

final class SaveMenuController
{
    public function __invoke(Request $request)
    {
        $form = $request->validate([
            'form' => ['required', 'array'],
        ])['form'];

        $game = MapGame::resolve();

        $game->saveMenu($form)->persist();

        return true;
    }
}
