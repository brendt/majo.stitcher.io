<?php

namespace App\Map\Tile;

final readonly class BorderStyle
{
    public function __construct(
        public string $color,
        public int $width = 2,
    ) {}
}
