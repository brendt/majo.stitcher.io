<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\HasTooltip;
use App\Map\Tile\SpecialTile\TradingPostTile;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;

final class LandTile extends BaseTile implements Upgradable, HasTooltip
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
        if ($this->elevation > 0.4 && $this->elevation <= 0.44) {
            $hex = $this->elevation;

            while ($hex <= 0.8) {
                $hex += 0.05;
            }

            $r = hex($hex);
            $g = hex($hex);
            $b = hex($hex / 2);

            return "#{$r}{$g}{$b}";
        }

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

    public function getTooltip(): string
    {
        return <<<HTML
        <div class="debug menu">
            Temperature: {$this->temperature}
            <br>
            Elevation: {$this->elevation}
        </div>
        HTML;
    }
}
