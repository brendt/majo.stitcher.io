<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Price;
use App\Map\Tile\CalculatesResourcePerTick;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\Purchasable;
use App\Map\Tile\Style\BorderStyle;

final class FishFarmerTile extends BaseTile implements HasResource, HasBorder, HandlesTick, HandlesClick, Purchasable, CalculatesResourcePerTick
{
    public function __construct(
        public readonly Point $point,
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
        return new BorderStyle('#FFFFFF55', 4);
    }

    public function handleTick(MapGame $game): Action
    {
        return (new UpdateResourceCount(fishCount: 1));
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(fishCount: 1);
    }

    public function getName(): string
    {
        return 'FishFarmerTile';
    }

    public function getPrice(MapGame $game): Price
    {
        return new Price(wood: 1);
    }

    public function getResourcePerTick(MapGame $game, Resource $resource): int
    {
        if ($resource !== Resource::Fish) {
            return 0;
        }

        return 1;
    }
}
