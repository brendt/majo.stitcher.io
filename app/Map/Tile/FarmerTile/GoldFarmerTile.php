<?php

namespace App\Map\Tile\FarmerTile;

use App\Map\Actions\Action;
use App\Map\Actions\DoNothing;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Price;
use App\Map\Tile\FarmerTile;
use App\Map\Tile\ResourceTile\GoldTile;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Tile;
use App\Map\Tile\Traits\BaseTileTrait;
use Exception;

final class GoldFarmerTile implements FarmerTile
{
    use BaseTileTrait;

    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFEC53', 4);
    }

    public function handleTick(MapGame $game): Action
    {
        $goldTile = $this->getGoldTile($game);

        $distance = $this->getPoint()->distanceBetween($goldTile->getPoint());

        if (rand(1, $distance) !== 1) {
            return new DoNothing();
        }

        return (new UpdateResourceCount(goldCount: 1));
    }

    public function getResource(): Resource
    {
        return Resource::Gold;
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(goldCount: 1);
    }

    public function getName(): string
    {
        return 'GoldFarmerTile';
    }

    public function getPrice(MapGame $game): Price
    {
        $goldTile = $this->getGoldTile($game);

        return new Price(wood: 10 - $this->getPoint()->distanceBetween($goldTile->getPoint()));
    }

    private function getGoldTile(MapGame $game): GoldTile
    {
        $goldTile = $game->findClosestTo(
            tile: $this,
            filter: fn(Tile $tile) => $tile instanceof GoldTile,
            radius: 5,
        );

        if (! $goldTile instanceof GoldTile) {
            throw new Exception('No gold tile found');
        }

        return $goldTile;
    }

    public function getResourcePerTick(MapGame $game, Resource $resource): int
    {
        if ($resource !== Resource::Gold) {
            return 0;
        }

        $action = $this->handleTick($game);

        if (! $action instanceof UpdateResourceCount) {
            return 0;
        }

        return $action->goldCount;
    }
}
