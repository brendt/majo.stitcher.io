<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\Style;
use App\Map\Tile\Tile;
use Spatie\Cloneable\Cloneable;

class BaseTile implements Tile
{
    use Cloneable;

    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly ?float $temperature = null,
        public readonly ?float $elevation = null,
        public readonly ?Biome $biome = null,
    ) {}

    public function getColor(): string
    {
        return '#fff';
    }

    public function getStyle(): Style
    {
        $backgroundColor = $this->getColor();
        $borderStyle = $this instanceof HasBorder ? $this->getBorderStyle() : null;
        $borderWidth = $borderStyle->width ?? 0;
        $borderColor = $borderStyle->color ?? '';

        return new Style(
            style: <<<EOF
            grid-area: $this->y / $this->x / $this->y / $this->x;
            --tile-color: $backgroundColor;
            --tile-border-width: {$borderWidth}px;
            --tile-border-color: {$borderColor};
            EOF,
            class: implode(
                ' ',
                [
                    $this instanceof HasBorder ? 'tile-border' : '',
                    $this instanceof HandlesClick ? 'clickable' : 'unclickable',
                ],
            )
        );
    }

    public function setTemperature(float $temperature): self
    {
        return $this->with(
            temperature: $temperature,
        );
    }

    public function setElevation(float $elevation): self
    {
        return $this->with(
            elevation: $elevation,
        );
    }

    public function setBiome(Biome $biome): self
    {
        return $this->with(
            biome: $biome,
        );
    }

    public function getBiome(): ?Biome
    {
        return $this->biome;
    }

    public function getX(): int
    {
        return $this->x ?? dd($this);
    }

    public function getY(): int
    {
        return $this->y;
    }

    public function toArray(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'style' => (array) $this->getStyle(),
            'name' => $this::class,
            'biome' => $this->getBiome()::class,
            'elevation' => $this->elevation,
            'temperature' => $this->temperature,
        ];
    }
}
