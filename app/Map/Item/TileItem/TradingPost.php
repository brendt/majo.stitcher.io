<?php

namespace App\Map\Item\TileItem;

use App\Map\Item\HasMenu;
use App\Map\Item\ItemPrice;
use App\Map\Item\TileItem;
use App\Map\MapGame;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\GenericTile\WaterTile;
use App\Map\Tile\Tile;
use Illuminate\View\View;

final class TradingPost implements TileItem, HasMenu
{
    private bool $menuShown = false;

    public function getId(): string
    {
        return 'TradingPost';
    }

    public function getName(): string
    {
        return 'Trading Post';
    }

    public function getPrice(): ItemPrice
    {
        return new ItemPrice();
    }

    public function canInteract(MapGame $game, Tile $tile): bool
    {
        if (! $tile instanceof LandTile) {
            return false;
        }

        $neighbours = $game->getNeighbours($tile);

        foreach ($neighbours as $tile) {
            if ($tile instanceof WaterTile) {
                return true;
            }
        }

        return false;
    }

    public function handleTicks(MapGame $game, Tile $tile, int $ticks): void {}

    public function getModifier(): int
    {
        return 1;
    }

    public function menuShown(): bool
    {
        return $this->menuShown;
    }

    public function toggleMenu(): void
    {
        $this->menuShown = ! $this->menuShown;
    }

    public function saveMenu(): void
    {
        $this->menuShown = false;
    }

    public function getMenu(): View
    {
        return view('mapGame.tradingPostMenu');
    }
}
