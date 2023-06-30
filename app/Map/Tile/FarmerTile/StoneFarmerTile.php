<?php

namespace App\Map\Tile\FarmerTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Price;
use App\Map\Tile\FarmerTile;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Traits\BaseTileTrait;

final class StoneFarmerTile implements FarmerTile
{
    use BaseTileTrait;

    public function __construct(
        public readonly Point $point,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public readonly float $noise,
    ) {}

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

    public function getResourcePerTick(MapGame $game, Resource $resource): int
    {
        return 0;
    }

    public function getName(): string
    {
        return 'StoneFarmerTile';
    }

    public function getPrice(MapGame $game): Price
    {
        return new Price(
            wood: 1,
        );
    }
}
