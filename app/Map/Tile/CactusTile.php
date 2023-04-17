<?php

namespace App\Map\Tile;

final class CactusTile extends BaseTile implements WithBorder
{
    public function __construct(
        public readonly float $noise,
    ) {}

    public function getColor(): string
    {
        return 'darkgreen';
    }

    public function getBorderColor(): string
    {
        return '#5FCC7BDD';
    }
}
