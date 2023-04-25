<?php

namespace App\Map\Tile;

use App\Map\MapGame;
use App\Map\Price;

interface Upgradable extends HasMenu
{
    public function canUpgrade(MapGame $game): bool;

    public function getUpgradePrice(): Price;

    public function getUpgradeTile(): Tile;
}
