<?php

namespace App\Http\Controllers;

class MapController
{
    public function __invoke(?string $seed = null)
    {
        return view('map', [
            'seed' => $seed ?? time(),
        ]);
    }
}
