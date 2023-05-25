<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Tile\HasTooltip;
use App\Map\Tile\Style;
use App\Map\Tile\Tile;

final readonly class DebugTile implements Tile, HasTooltip
{

    public function __construct(
        public int $x,
        public int $y,
        public float $noise,
        public array $debug = [],
    ) {}

    public function getColor(): string
    {
        $hex = dechex($this->noise * 255);

        if (strlen($hex) < 2) {
            $hex = "0" . $hex;
        }

        return "#{$hex}{$hex}{$hex}";
    }

    public function getBiome(): ?Biome
    {
        return null;
    }

    public function getX(): int
    {
        $this->x;
    }

    public function getY(): int
    {
        $this->y;
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
        return new Point($this->getX(), $this->getY());
    }
}
