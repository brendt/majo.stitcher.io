<?php

namespace App\Map\Tile;

final readonly class Style
{
    public function __construct(
        public string $style = '',
        public string $class = '',
    ) {}
}
