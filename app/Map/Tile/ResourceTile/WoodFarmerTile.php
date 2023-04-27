<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\AddInventoryItem;
use App\Map\Actions\Combine;
use App\Map\Actions\DoNothing;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\Inventory\Item\Seed;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;

final class WoodFarmerTile extends BaseTile implements HasResource, HasBorder, HandlesTicks, HandlesClick, Upgradable
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
                random_int(1, 50) === 1 ? new AddInventoryItem(new Seed()) : new DoNothing(),
            );
        }

        $randomTile = $surroundingTiles[array_rand($surroundingTiles)];

        if ($randomTile instanceof WoodTile && $randomTile->state === WoodTileState::GROWING) {
            $randomTile->markAsGrown();
        }

        return new DoNothing();
    }

    public function getColor(): string
    {
        return "#00FF00";
    }

    public function getResource(): Resource
    {
        return Resource::Wood;
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(woodCount: 1);
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

    public function getUpgradePrice(): Price
    {
        return new Price(wood: 1);
    }

    public function getUpgradeTile(): Tile
    {
        return new WoodFarmerXLTile(...(array) $this);
    }

    public function canUpgrade(MapGame $game): bool
    {
        return true;
    }
}
