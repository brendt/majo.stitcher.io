<?php

namespace App\Map\Tile;

use Illuminate\Contracts\View\View;

interface HasTooltip
{
    public function getTooltip(): View;
}
