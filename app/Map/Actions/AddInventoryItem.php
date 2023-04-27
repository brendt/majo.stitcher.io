<?php

namespace App\Map\Actions;

use App\Map\Inventory\Item;
use App\Map\MapGame;

final readonly class AddInventoryItem implements Action
{
    public function __construct(private Item $item) {}

    public function __invoke(MapGame $game): void
    {
        $game->inventory->add($this->item);
    }
}
