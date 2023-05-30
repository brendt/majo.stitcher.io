<?php

namespace App\Map\Tile;

use App\Map\MapGame;
use App\Map\Price;

interface Upgradable extends HasMenu
{
    /**
     * @param MapGame $game
     * @return Purchasable[]
     */
    public function canUpgradeTo(MapGame $game): array;


//    public function canUpgrade(MapGame $game): bool;
//
//    public function getUpgradePrice(MapGame $game): Price;
//
//    public function getUpgradeTile(MapGame $game): Tile;
}
