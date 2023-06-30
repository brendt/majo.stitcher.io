<?php

namespace App\Map\Inventory\Item;

use App\Map\Inventory\ItemForTile;
use App\Map\MapGame;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\ResourceTile\WoodTile;
use App\Map\Tile\ResourceTile\WoodTileState;
use App\Map\Tile\Tile;

final class Seed implements ItemForTile
{
    public function getId(): string
    {
        return 'Seed';
    }

    public function getName(): string
    {
        return 'Seed';
    }

    public function canBeUsedOn(Tile $tile, MapGame $game): bool
    {
        return $tile instanceof LandTile;
    }

    public function useOn(Tile $tile, MapGame $game): void
    {
        if (! $tile instanceof LandTile) {
            return;
        }

        if (! $this->canBeUsedOn($tile, $game)) {
            return;
        }

        $woodTile = new WoodTile(
            point: $tile->getPoint(),
            elevation: $tile->elevation,
            biome: $tile->getBiome(),
            state: WoodTileState::GROWING,
            timeGrowing: 0
        );

        $game->setTile($tile->getPoint(), $woodTile);
    }
}
