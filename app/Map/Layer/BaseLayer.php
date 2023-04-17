<?php

namespace App\Map\Layer;


use App\Map\Tile\BaseTile;

final class BaseLayer
{
    /** @var \App\Map\Layer\Layer[] */
    private array $layers = [];

    /** @var \App\Map\Tile\Tile[][] */
    private array $board = [];

    public function __construct(
        public readonly int $width,
        public readonly int $height,
    ) {}

    public function generate(): array
    {
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $tile = new BaseTile($x, $y);

                foreach ($this->layers as $layer) {
                    $tile = $layer->generate($tile, $this);
                }

                $this->board[$x][$y] = $tile;
            }
        }

        return $this->board;
    }

    public function get(int $x, int $y): ?object
    {
        return $this->board[$x][$y] ?? null;
    }

    public function add(Layer $layer): self
    {
        $this->layers[] = $layer;

        return $this;
    }
}
