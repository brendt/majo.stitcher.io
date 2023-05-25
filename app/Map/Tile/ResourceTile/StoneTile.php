<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\HasTooltip;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;
use Illuminate\Contracts\View\View;

final class StoneTile extends BaseTile implements HasResource, HasBorder, HandlesClick, Upgradable, HasTooltip
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public readonly float $noise,
    ) {}

    public function getResource(): Resource
    {
        return Resource::Stone;
    }

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value > 0.8) {
            $value -= 0.3;
        }

        $hex = hex($value);

        return "#{$hex}{$hex}{$hex}";
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#333333');
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(stoneCount: 1);
    }

    public function getMenu(): Menu
    {
        return new Menu(
            hasMenu: $this,
            viewPath: 'menu.upgrade',
            viewData: [
                'tile' => $this,
            ],
        );
    }

    public function getUpgradePrice(): Price
    {
        return new Price(wood: 1);
    }

    public function getUpgradeTile(): Tile
    {
        return new StoneFarmerTile(...(array) $this);
    }

    public function canUpgrade(MapGame $game): bool
    {
        return true;
    }

    public function getTooltip(): string
    {
        return <<<HTML
        <div class="debug menu">
            Tile: StoneTile
        </div>
        HTML;
    }
}