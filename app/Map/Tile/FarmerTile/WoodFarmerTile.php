<?php

namespace App\Map\Tile\FarmerTile;

use App\Map\Actions\Action;
use App\Map\Actions\AddInventoryItem;
use App\Map\Actions\Combine;
use App\Map\Actions\DoNothing;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\Inventory\Item\Seed;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Price;
use App\Map\Tile\CalculatesResourcePerTick;
use App\Map\Tile\FarmerTile;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\Purchasable;
use App\Map\Tile\ResourceTile\Resource;
use App\Map\Tile\ResourceTile\WoodTile;
use App\Map\Tile\ResourceTile\WoodTileState;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Traits\BaseTileTrait;

final class WoodFarmerTile implements FarmerTile
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
        return new BorderStyle('#B66F27DD', 4);
    }

    public function handleTick(MapGame $game): Action
    {
        $surroundingTiles = $game->getNeighbours($this, 2);

        foreach ($surroundingTiles as $tile) {
            if (! $tile instanceof WoodTile) {
                continue;
            }

            if ($tile->state !== WoodTileState::GROWN) {
                continue;
            }

            $tile->markAsGrowing();

            return new Combine(
                new UpdateResourceCount(woodCount: 1),
                random_int(1, 20) === 1 ? new AddInventoryItem(new Seed()) : new DoNothing(),
            );
        }

        $randomTile = $surroundingTiles[array_rand($surroundingTiles)];

        if ($randomTile instanceof WoodTile && $randomTile->state === WoodTileState::GROWING) {
            $randomTile->markAsGrown();
        }

        return new DoNothing();
    }

    public function getResource(): Resource
    {
        return Resource::Wood;
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(woodCount: 1);
    }

    public function getPrice(MapGame $game): Price
    {
        return new Price(wood: 1);
    }

    public function getName(): string
    {
        return 'WoodFarmerTile';
    }

    public function getResourcePerTick(MapGame $game, Resource $resource): int
    {
        return $resource === Resource::Wood
            ? 1
            : 0;
    }
}
