<?php

namespace App\Http\Controllers;


final class MapJSController
{
    public function __invoke()
    {
        return view('mapJsScript', []);
    }
}
