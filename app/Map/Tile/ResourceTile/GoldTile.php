<?php

namespace App\Map\Tile\ResourceTile;

use App\Map\Actions\Action;
use App\Map\Actions\UpdateResourceCount;
use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasResource;
use App\Map\Tile\Style\BorderStyle;

final class GoldTile extends BaseTile implements HasResource, HasBorder, HandlesClick
{
    public function __construct(
        public readonly Point $point,
        public readonly float $elevation,
        public readonly Biome $biome,
    ) {}

    public function getResource(): Resource
    {
        return Resource::Gold;
    }

    public function getColor(): string
    {
        return 'gold';

        $value = $this->elevation;

        while ($value > 0.8) {
            $value -= 0.3;
        }

        $hex = hex($value);

        return "#{$hex}{$hex}{$hex}";
    }

    public function getBorderStyle(): BorderStyle
    {
        return new BorderStyle('#FFEC53');
    }

    public function handleClick(MapGame $game): Action
    {
        return new UpdateResourceCount(goldCount: 1);
    }
}
