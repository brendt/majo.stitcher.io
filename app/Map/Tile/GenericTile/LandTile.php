<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\SpecialTile\TradingPostTile;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;

final class LandTile extends BaseTile implements Upgradable
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
    ) {}

    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        return $this->getBiome()->getGrassColor($this);
    }

    public function getMenu(): Menu
    {
        return new Menu(
            hasMenu: $this,
            viewPath: 'menu.upgrade',
            viewData: ['tile' => $this],
        );
    }

    public function getUpgradePrice(): Price
    {
        return new Price(wood: 1);
    }

    public function getUpgradeTile(): Tile
    {
        return new TradingPostTile(...(array) $this);
    }

    public function canUpgrade(MapGame $game): bool
    {
        foreach ($game->getNeighbours($this) as $neighbour) {
            if ($neighbour instanceof WaterTile) {
                return true;
            }
        }

        return false;
    }
}
