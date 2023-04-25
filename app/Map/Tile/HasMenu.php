<?php

namespace App\Map\Tile;

use App\Map\Menu;

interface HasMenu
{
    public function getMenu(): Menu;
}
