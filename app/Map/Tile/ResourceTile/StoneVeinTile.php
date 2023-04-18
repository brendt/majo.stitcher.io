<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Item\Item;
use App\Map\MapGame;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Clickable;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\WithBorder;

final class StoneVeinTile extends BaseTile implements WithBorder, Clickable, HandlesTicks
{
    public function __construct(
        public readonly float $noise,
        public ?Item $item = null,
    ) {}

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value > 0.8) {
            $value -= 0.3;
        }

        $hex = hex($value);

        return "#{$hex}{$hex}{$hex}";
    }

    public function getBorderColor(): string
    {
        return '#333';
    }

    public function handleClick(MapGame $game): void
    {
        $selectedItem = $game->selectedItem;

        if ($selectedItem?->canInteract($this) && $this->item === null) {
            $this->item = $selectedItem;
            $game->unselectItem();
        } else {
            $game->stoneCount += 1;
        }
    }

    public function canClick(MapGame $game): bool
    {
        $selectedItem = $game->selectedItem;

        if ($selectedItem) {
            return $selectedItem->canInteract($this) && $this->item === null;
        }

        return true;
    }

    public function handleTicks(MapGame $game, int $ticks): void
    {
        $this->item?->handleTicks($game, $this, $ticks);
    }
}
