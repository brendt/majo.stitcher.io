<?php

namespace App\Map\Tile;

final class FishTile extends BaseTile implements WithBorder
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

        return "#0000{$hex}";
    }

    public function getBorderColor(): string
    {
        return '#FFFFFF55';
    }
}
