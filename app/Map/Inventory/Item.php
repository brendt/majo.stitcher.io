<?php

namespace App\Map\Inventory;

use App\Map\MapGame;
use App\Map\Price;
use App\Map\Tile\Tile;

interface Item
{
    public function getId(): string;

    public function getName(): string;
}
