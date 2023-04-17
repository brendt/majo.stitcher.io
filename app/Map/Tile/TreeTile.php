<?php

namespace App\Map\Tile;

final class TreeTile extends BaseTile
{
    public function __construct(
        public readonly float $noise,
    ) {}

    public function getColor(): string
    {
        $value = $this->noise;

        while ($value < 0.6) {
            $value += 0.1;
        }

        $hex = hex($value);

        return "#00{$hex}00";
    }
}
