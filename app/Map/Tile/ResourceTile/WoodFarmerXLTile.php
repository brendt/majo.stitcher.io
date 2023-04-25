<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;

final class WoodFarmerXLTile extends BaseTile implements HasResource, HasBorder, HandlesClick, HandlesTicks
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public readonly float $noise,
    ) {}

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#B66F27DD', 6);
    }

    public function handleTicks(MapGame $game, int $ticks): Action
    {
        return (new UpdateResourceCount(woodCount: $ticks * 10));
    }

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value < 0.6) {
            $value += 0.1;
        }

        $hex = hex($value);

        return "#00{$hex}00";
    }

    public function getResource(): Resource
    {
        return Resource::Wood;
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(woodCount: 1);
    }
}
