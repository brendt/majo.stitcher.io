<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Point;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\Style\BorderStyle;

final class StoneFarmerTile extends BaseTile implements HasResource, HasBorder, HandlesTick, HandlesClick
{
    public function __construct(
        public readonly Point $point,
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
        return new BorderStyle('#333333', 4);
    }

    public function handleTick(MapGame $game): Action
    {
        return (new UpdateResourceCount(stoneCount: 1));
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
            hasMenu: $this,
            viewPath: 'menu.upgrade',
            viewData: [
                'tile' => $this,
            ],
        );
    }
}
