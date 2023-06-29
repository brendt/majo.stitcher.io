<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Point;
use App\Map\Tile\HasTooltip;

final class WaterTile extends BaseTile implements HasTooltip
{
    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}

    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        return $this->getBiome()->getTileColor($this);
    }
}
