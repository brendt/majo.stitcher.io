<?php

namespace App\Map\Tile\ResourceTile;

enum Resource: string
{
    case Fish = 'Fish';
    case Flax = 'Flax';
    case Stone = 'Stone';
    case Gold = 'Gold';
    case Wood = 'Wood';

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

    public function getBaseColor(): string
    {
        return match ($this) {
            self::Fish => 'blue',
            self::Flax => 'white',
            self::Stone => 'gray',
            self::Gold => 'gold',
            self::Wood => 'brown',
        };
    }

}
