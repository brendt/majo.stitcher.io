<?php

namespace App\Map\Tile\GenericTile;

use App\Map\Biome\Biome;
use App\Map\Biome\DebugBiome;
use App\Map\Inventory\ItemForTile;
use App\Map\MapGame;
use App\Map\Point;
use App\Map\Tile\HandlesClick;
use App\Map\Tile\HasBorder;
use App\Map\Tile\HasMenu;
use App\Map\Tile\Style\Style;
use App\Map\Tile\Tile;
use App\Map\Tile\Upgradable;
use Spatie\Cloneable\Cloneable;

class BaseTile implements Tile
{
    use Cloneable;

    public function __construct(
        public readonly Point $point,
    ) {
    }

    public function getColor(): string
    {
        return '#fff';
    }

    public function isClickable(MapGame $game): bool
    {
        if ($game->selectedItem instanceof ItemForTile) {
            return $game->selectedItem->canBeUsedOn($this, $game);
        }

        if ($this instanceof HandlesClick) {
            return true;
        }

        if ($this instanceof Upgradable) {
            return $this->canUpgradeTo($game) !== [];
        }

        if ($this instanceof HasMenu) {
            return true;
        }

        return false;
    }

    public function getStyle(MapGame $game): Style
    {
        $backgroundColor = $this->getColor();
        $borderStyle = $this instanceof HasBorder ? $this->getBorderStyle() : null;
        $borderWidth = $borderStyle->width ?? 0;
        $borderColor = $borderStyle->color ?? '';
        $clickable = $this->isClickable($game);
        $slug = $this->getSlug();
        $x = $this->getPoint()->x;
        $y = $this->getPoint()->y;

        return new Style(
            style: <<<EOF
            grid-area: $y / $x / $y / $x;
            --tile-color: $backgroundColor;
            --tile-border-width: {$borderWidth}px;
            --tile-border-color: {$borderColor};
            EOF,
            class: implode(
                ' ',
                [
                    $this instanceof HasBorder ? 'tile-border' : '',
                    $clickable ? 'clickable' : 'unclickable',
                    "tile-{$slug}",
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

    public function getBiome(): Biome
    {
        return $this->biome ?? new DebugBiome();
    }

    public function getSlug(): string
    {
        $class = $this::class;

        $parts = explode('\\', $class);

        return $parts[array_key_last($parts)];
    }

    public function toArray(MapGame $game): array
    {
        return [
            'x' => $this->getPoint()->x,
            'y' => $this->getPoint()->y,
            'style' => (array)$this->getStyle($game),
            'name' => $this::class,
            'biome' => $this->getBiome() ? $this->getBiome()::class : null,
            'elevation' => $this->elevation,
            'tooltip' => $this->getTooltip(),
        ];
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getTooltip(): string
    {
        $class = static::class;

        $biome = $this->getBiome() ? $this->getBiome()::class : '';

        return <<<HTML
        <div class="debug menu">
            Tile: {$class}
            <br>
            Biome: {$biome}
        </div>
        HTML;
    }
}
