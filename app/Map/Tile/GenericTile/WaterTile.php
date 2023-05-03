<?php

namespace App\Map\Tile\GenericTile;

final class WaterTile extends BaseTile
{
    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        $elevation = $this->elevation;

        while ($elevation < 0.25) {
            $elevation += 0.01;
        }

        $r = hex($elevation / 3);
        $g = hex($elevation / 3);
        $b = hex($elevation);

        return "#{$r}{$g}{$b}";
    }
}
