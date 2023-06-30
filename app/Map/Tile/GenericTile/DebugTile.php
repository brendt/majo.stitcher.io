<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Biome\PlainsBiome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Tile\Style\Style;
use App\Map\Tile\Tile;

final readonly class DebugTile implements Tile
{
    public function __construct(
        public Point $point,
        public float $noise,
        public array $debug = [],
    ) {}

    public function getBiome(): Biome
    {
        return new PlainsBiome();
    }

    public function getStyle(MapGame $game): Style
    {
        return new Style();
    }

    public function toArray(MapGame $game): array
    {
        return [];
    }

    public function getTooltip(): string
    {
        $debug = '';

        foreach ($this->debug as $key => $value) {
            $debug .= "{$key}: {$value}<br>";
        }

        return <<<HTML
        <div class="debug menu">
            Noise: {$this->noise}
            <br>
            {$debug}
        </div>
        HTML;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }
}
