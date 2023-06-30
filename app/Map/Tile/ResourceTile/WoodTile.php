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
use App\Map\Point;
use App\Map\Tile\FarmerTile\WoodFarmerTile;
use App\Map\Tile\HandlesTick;
use App\Map\Tile\ResourceTile;
use App\Map\Tile\Style\BorderStyle;
use App\Map\Tile\Traits\BaseTileTrait;
use App\Map\Tile\Upgradable;

final class WoodTile implements ResourceTile, HandlesTick, Upgradable
{
    use BaseTileTrait;

    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
        public WoodTileState $state = WoodTileState::GROWN,
        public int $timeGrowing = 0,
    ) {}

    public function getResource(): Resource
    {
        return Resource::Wood;
    }

    public function getBorderStyle(): BorderStyle
    {
        return match ($this->state) {
            WoodTileState::GROWN => new BorderStyle('#B66F27DD'),
            WoodTileState::GROWING => new BorderStyle('#B66F2733'),
            WoodTileState::PLANTED => new BorderStyle('#B66F2766'),
        };
    }

    public function handleClick(MapGame $game): Action
    {
        if ($this->state !== WoodTileState::GROWN) {
            return new DoNothing();
        }

        $this->markAsGrowing();

        return new Combine(
            new UpdateResourceCount(woodCount: 1),
            random_int(1, 2) === 1 ? new AddInventoryItem(new Seed()) : new DoNothing(),
        );
    }

    public function markAsGrowing(): void
    {
        $this->state = WoodTileState::GROWING;
        $this->timeGrowing = 0;
    }

    public function markAsGrown(): void
    {
        $this->state = WoodTileState::GROWN;
        $this->timeGrowing = 0;
    }

    public function handleTick(MapGame $game): Action
    {
        if ($this->state !== WoodTileState::GROWING) {
            return new DoNothing();
        }

        $this->timeGrowing += 1;

        if ($this->timeGrowing >= 40) {
            $this->markAsGrown();
        }

        return new DoNothing();
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

    public function getTooltip(): string
    {
        return <<<HTML
        <div class="debug menu">
            Tile: Woodtile
        </div>
        HTML;
    }

    public function canUpgradeTo(MapGame $game): array
    {
        return [
            new WoodFarmerTile(
                point: $this->point,
                temperature: $this->elevation,
                elevation: $this->elevation,
                biome: $this->biome,
                noise: $this->elevation,
            ),
        ];
    }
}
