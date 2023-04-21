<?php

namespace App\Map\Tile\ResourceTile;

enum Resource
{
    case Fish;
    case Flax;
    case Stone;
    case Gold;
    case Wood;

    public function getCountPropertyName(): string
    {
        return match ($this) {
            self::Fish => 'fishCount',
            self::Flax => 'flaxCount',
            self::Stone => 'stoneCount',
            self::Gold => 'goldCount',
            self::Wood => 'woodCount',
        };
    }
}
