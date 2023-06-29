<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\BeachBiome;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Point;
use App\Map\Tile\HasTooltip;
use App\Map\Tile\ResourceTile\GoldFarmerTile;
use App\Map\Tile\ResourceTile\GoldTile;
use App\Map\Tile\SpecialTile\FishingShackTile;
use App\Map\Tile\SpecialTile\TradingPostTile;
use App\Map\Tile\Upgradable;

final class LandTile extends BaseTile implements HasTooltip, Upgradable
{
    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}

    public function getColor(): string
    {
        return $this->getBiome()->getTileColor($this);
    }

    public function getMenu(): Menu
    {
        return new Menu(
            hasMenu: $this,
            viewPath: 'menu.upgrade',
            viewData: ['tile' => $this],
        );
    }

    public function canUpgradeTo(MapGame $game): array
    {
        $canUpgradeTo = [];

        if ($this->getBiome() instanceof BeachBiome) {
            $canUpgradeTo[] = new FishingShackTile($this->point);
        }

        foreach ($game->getNeighbours($this) as $neighbour) {
            if ($neighbour instanceof WaterTile) {
                $canUpgradeTo[] = new TradingPostTile($this->point);
                break;
            }
        }

        foreach ($game->getNeighbours($this, 5) as $neighbour) {
            if ($neighbour instanceof GoldTile) {
                $canUpgradeTo[] = new GoldFarmerTile($this->point, $this->elevation, $this->biome);
                break;
            }
        }

        return $canUpgradeTo;
    }
}
