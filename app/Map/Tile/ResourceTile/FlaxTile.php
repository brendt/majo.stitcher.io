<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Tile\ResourceTile;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Traits\BaseTileTrait;

final class FlaxTile implements ResourceTile
{
    use BaseTileTrait;

    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}

    public function getResource(): Resource
    {
        return Resource::Flax;
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFFFFF66');
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(flaxCount: 1);
    }
}
