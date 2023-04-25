<?php

namespace App\Map\Tile;

use App\Map\Biome\Biome;

interface Tile
{
    public function getX(): int;

    public function getY(): int;

    public function getStyle(): Style;

    public function getBiome(): ?Biome;

    public function toArray(): array;
}
