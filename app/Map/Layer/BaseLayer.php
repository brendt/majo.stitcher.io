<?php

namespace App\Map\Layer;

use App\Map\Point;
use App\Map\Tile\GenericTile\BaseTile;
use App\Map\Tile\Tile;
use Generator;
use TypeError;

final class BaseLayer
{
    /** @var callable[] */
    private array $pendingLayers = [];

    /** @var Tile[][] */
    private array $board = [];

    public function __construct(
        public readonly int $width,
        public readonly int $height,
    ) {}

    public function generate(): self
    {
        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $point = new Point($x, $y);

                $tile = $this->get($point) ?? new BaseTile($point);

                foreach ($this->pendingLayers as $layer) {
                    try {
                        $tile = $layer($tile, $this);
                    } catch (TypeError) {
                        continue;
                    }
                }

                $this->board[$x][$y] = $tile;
            }
        }

        $this->pendingLayers = [];

        return $this;
    }

    /**
     * @return Tile[][]
     */
    public function getBoard(): array
    {
        return $this->board;
    }

    public function get(Point $point): ?object
    {
        return $this->board[$point->x][$point->y] ?? null;
    }

    public function remove(Point $point): void
    {
        unset($this->board[$point->x][$point->y]);
    }

    public function add(callable $layer): self
    {
        $this->pendingLayers[] = $layer;

        return $this;
    }

    public function loop(): Generator
    {
        foreach ($this->board as $row) {
            foreach ($row as $tile) {
                yield $tile;
            }
        }
    }
}
