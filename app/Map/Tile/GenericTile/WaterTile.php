<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Tile\HasTooltip;

final class WaterTile extends BaseTile implements HasTooltip
{
    public static function fromBase(BaseTile $tile): self
    {
        return new self(...(array) $tile);
    }

    public function getColor(): string
    {
        return $this->getBiome()->getTileColor($this);
    }

    public function getTooltip(): string
    {
        $class = static::class;
        $biome = $this->getBiome()::class;

        return <<<HTML
        <div class="debug menu">
            Temperature: {$this->temperature}
            <br>
            Elevation: {$this->elevation}
            <br>
            Tile: {$class}
            <br>
            Biome: {$biome}
        </div>
        HTML;
    }
}
