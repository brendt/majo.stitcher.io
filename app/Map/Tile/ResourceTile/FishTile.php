<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Point;
use App\Map\Tile\FarmerTile\FishFarmerTile;
use App\Map\Tile\ResourceTile;
use App\Map\Tile\SpecialTile\FishingShackTile;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Tile;
use App\Map\Tile\Traits\BaseTileTrait;
use App\Map\Tile\Upgradable;

final class FishTile implements ResourceTile, Upgradable
{
    use BaseTileTrait;

    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}

    public function getResource(): Resource
    {
        return Resource::Fish;
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFFFFF55');
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(fishCount: 1);
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

    public function canUpgradeTo(MapGame $game): array
    {
        $fishingShackTile = $this->getFishingShackTile($game);

        if ($fishingShackTile) {
            return [new FishFarmerTile($this->point, $this->elevation, $this->elevation, $this->biome, $this->elevation)];
        }

        return [];
    }

    private function getFishingShackTile(MapGame $game): ?FishingShackTile
    {
        $fishingShackTile = $game->findClosestTo(
            tile: $this,
            filter: fn(Tile $tile) => $tile instanceof FishingShackTile,
            radius: 12,
        );

        if (! $fishingShackTile instanceof FishingShackTile) {
            return null;
        }

        return $fishingShackTile;
    }
}
