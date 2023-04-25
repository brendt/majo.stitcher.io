<?php

namespace App\Map\Tile;

interface HasBorder
{
    public function getBorderStyle(): BorderStyle;
}
