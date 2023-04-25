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
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;

final class FishTile extends BaseTile implements HasResource, HasBorder, HandlesClick, Upgradable
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
        return Resource::Fish;
    }

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value < 0.6) {
            $value += 0.1;
        }

        $hex = hex($value);

        return "#0000{$hex}";
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFFFFF55');
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(fishCount: 1);
    }

    public function getMenu(): Menu
    {
        return new Menu(
            'menu.upgrade',
            ['tile' => $this],
        );
    }

    public function getUpgradePrice(): Price
    {
        return new Price(wood: 1);
    }

    public function getUpgradeTile(): Tile
    {
        return new FishFarmerTile(...(array) $this);
    }
}
