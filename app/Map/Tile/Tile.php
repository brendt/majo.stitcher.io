<?php

namespace App\Map\Tile;

use App\Map\Biome\Biome;
use App\Map\MapGame;

interface Tile
{
    public function getX(): int;

    public function getY(): int;

    public function getStyle(MapGame $game): Style;

    public function getBiome(): ?Biome;

    public function toArray(MapGame $game): array;
}
