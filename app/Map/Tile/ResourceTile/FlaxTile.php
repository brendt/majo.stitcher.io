<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Item\Item;
use App\Map\MapGame;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Clickable;
use App\Map\Tile\HandlesTicks;
use App\Map\Tile\WithBorder;

final class FlaxTile extends BaseTile implements WithBorder, Clickable, HandlesTicks
{
    public function __construct(
        public readonly float $noise,
        public ?Item $item = null,
    ) {}

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value < 0.6) {
            $value += 0.1;
        }

        $r = hex($value / 2);
        $g = hex($value);
        $b = hex($value / 2);

        return "#{$r}{$g}{$b}";
    }

    public function getBorderColor(): string
    {
        return '#FFFFFF66';
    }

    public function handleClick(MapGame $game): void
    {
        $game->flaxCount += 1;
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
