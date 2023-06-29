<?php

namespace App\Map\Tile;

use App\Map\Tile\Style\BorderStyle;

interface HasBorder
{
    public function getBorderStyle(): BorderStyle;
}
