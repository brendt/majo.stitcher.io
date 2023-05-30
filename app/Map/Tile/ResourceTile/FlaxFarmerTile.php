<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;

final class FlaxFarmerTile extends BaseTile implements HasResource, HasBorder, HandlesTicks, HandlesClick
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
        return Resource::Flax;
    }

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value < 0.6) {
            $value += 0.1;
        }

        $r = hex($value / 2);
        $g = hex($value);
        $b = hex($value / 2);

        return "#{$r}{$g}{$b}";
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFFFFF66', 4);
    }

    public function handleTick(MapGame $game): Action
    {
        return (new UpdateResourceCount(flaxCount: 1));
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(flaxCount: 1);
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
