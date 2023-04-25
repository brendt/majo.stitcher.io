<?php

namespace App\Map\Tile;

use App\Map\Price;

interface Upgradable extends HasMenu
{
    public function getUpgradePrice(): Price;

    public function getUpgradeTile(): Tile;
}
