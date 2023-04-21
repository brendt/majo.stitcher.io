<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Item\HasMenu;
use App\Map\Item\TileItem;
use App\Map\MapGame;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HasBorder;

final class LandTile extends BaseTile implements HandlesClick, HasBorder
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature,
        public readonly ?float $elevation,
        public readonly ?Biome $biome,
        public ?TileItem $item = null,
    ) {}

    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        return $this->getBiome()->getGrassColor($this);
    }

    public function handleClick(MapGame $game): void
    {
        $selectedItem = $game->selectedItem;

        if ($selectedItem?->canInteract($game, $this) && $this->item === null) {
            $this->item = $selectedItem;
            $game->buyItem($selectedItem);

            return;
        }

        if ($this->item instanceof HasMenu) {
            $this->item->toggleMenu();
        }
    }

    public function canClick(MapGame $game): bool
    {
        $selectedItem = $game->selectedItem;

        if ($selectedItem) {
            return $selectedItem->canInteract($game, $this) && $this->item === null;
        }

        return true;
    }

    public function getBorderColor(): string
    {
        if ($this->item) {
            return 'red';
        }

        return '';
    }
}
