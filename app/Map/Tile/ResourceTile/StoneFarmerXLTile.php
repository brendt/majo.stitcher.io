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
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;

final class StoneFarmerXLTile extends BaseTile implements HasResource, HasBorder, HandlesTicks, HandlesClick
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public readonly float $noise,
    ) {}

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
        return new BorderStyle('#333333', 6);
    }

    public function handleTicks(MapGame $game, int $ticks): Action
    {
        return (new UpdateResourceCount(stoneCount: $ticks * 10));
    }

    public function getResource(): Resource
    {
        return Resource::Stone;
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(stoneCount: 1);
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
}
