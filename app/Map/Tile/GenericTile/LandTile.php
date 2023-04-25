<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Item\TileItem;

final class LandTile extends BaseTile
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

    public function getBorderColor(): string
    {
        if ($this->item) {
            return 'red';
        }

        return '';
    }
}
