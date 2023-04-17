<?php

namespace App\Map\Tile;

final class CactusTile extends BaseTile
{
    public function __construct(
        public readonly float $noise,
    ) {}

    public function getColor(): string
    {
        return 'darkgreen';
    }
}
