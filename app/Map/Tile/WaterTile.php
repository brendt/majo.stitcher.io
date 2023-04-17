<?php

namespace App\Map\Tile;

final class WaterTile extends BaseTile
{
    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        return $this->getBiome()->getWaterColor($this);
    }
}
