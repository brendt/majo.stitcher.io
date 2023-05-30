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
use App\Map\Tile\HasTooltip;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;
use Illuminate\Contracts\View\View;

final class WoodTile extends BaseTile implements HasResource, HasBorder, HandlesClick, HandlesTicks, HasTooltip, Upgradable
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public readonly float $noise,
        public WoodTileState $state = WoodTileState::GROWN,
        public int $timeGrowing = 0,
    ) {}

    public function getResource(): Resource
    {
        return Resource::Wood;
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
                x: $this->x,
                y: $this->y,
                temperature: $this->temperature,
                elevation: $this->elevation,
                biome: $this->biome,
                noise: $this->noise,
            ),
        ];
    }
}
