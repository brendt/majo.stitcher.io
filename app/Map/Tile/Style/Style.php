<?php

namespace App\Map\Tile\Style;

final readonly class Style
{
    public function __construct(
        public string $style = '',
        public string $class = '',
    ) {}
}
