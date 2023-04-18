<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Tile\GenericTile\BaseTile;

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
