<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Menu;
use App\Map\Price;
use App\Map\Tile\BorderStyle;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\SpecialTile\FishingShackTile;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;

final class FishTile extends BaseTile implements HasResource, HasBorder, HandlesClick, Upgradable
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
            return [new FishFarmerTile($this->x, $this->y, $this->temperature, $this->elevation, $this->biome, $this->noise)];
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
