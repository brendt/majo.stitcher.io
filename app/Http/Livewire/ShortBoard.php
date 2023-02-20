<?php

namespace App\Http\Livewire;

use Generator;
use Session;

/** @property \App\Http\Livewire\Tile[][][] $tiles */
final class ShortBoard implements Board
{
    private const HEIGHT = 5;

    public int $score = 0;

    public function __construct(
        public array $tiles = [],
    ) {}

    public static function resolve(): ?self
    {
        return Session::get('short-board');
    }

    public function reset(): self
    {
        Session::remove('short-board');

        return self::init();
    }

    public function persist(): void
    {
        Session::put('short-board', $this);
    }

    public static function init(): self
    {
        if ($board = self::resolve()) {
            return $board;
        }

        $values = [];

        foreach (range(1, self::HEIGHT) as $value) {
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
        }

        $board = new ShortBoard();

        shuffle($values);

        foreach (range(0, 1) as $x) {
            foreach (range(0, self::HEIGHT - 1) as $y) {
                $board->add(new Tile(
                    x: $x + 7,
                    y: $y,
                    z: 0,
                    value: current($values),
                    board: $board,
                ));

                next($values);
            }
        }

        $board->persist();

        return $board;
    }

    public function add(Tile $tile): self
    {
        $this->tiles[$tile->y][$tile->x][$tile->z] = $tile;

        return $this;
    }

    public function remove(Tile $tile): static
    {
        unset($this->tiles[$tile->y][$tile->x][$tile->z]);

        $this->score += (int) $tile->value;

        return $this;
    }

    public function getAvailablePairs(): int
    {
        $available = 0;

        foreach ($this->loop() as $tile) {
            $available += $tile->hasOpenMatch() ? 1 : 0;
        }

        return $available / 2;
    }

    public function handleEmptyRows(): self
    {
        if ($this->score > 600) {
            return $this;
        }

        /** @var \App\Http\Livewire\Tile[][][] $newTiles */
        $newTiles = [];

        foreach ($this->tiles as $row) {
            if ($this->isFullEmptyRow($row)) {
                continue;
            }

            $newTiles[] = $row;
        }

        while (count($newTiles) < self::HEIGHT) {
            $newTiles[] = $this->createNewLine();

            $this->score += 20;
        }

        foreach ($newTiles as $y => $row) {
            foreach ($row as $x => $tiles) {
                foreach ($tiles as $z => $tile) {
                    $newTiles[$y][$x][$z] = $tile->move($x, $y, $z);
                }
            }
        }

        $this->tiles = $newTiles;

        return $this;
    }

    public function get(int $x, int $y, ?int $z = null): ?Tile
    {
        if ($z === null) {
            $cell = $this->tiles[$y][$x] ?? [];

            if ($cell === []) {
                return null;
            }

            $z = max(array_keys($cell)) ?? null;
        }

        return $this->tiles[$y][$x][$z] ?? null;
    }

    public function createNewLine(): array
    {
        $pairs = match (true) {
            $this->score > 400 => 4,
            $this->score > 100 => 2,
            default => 1,
        };

        $maxValue = match (true) {
            $this->score > 600 => 12,
            $this->score > 500 => 10,
            $this->score > 300 => 9,
            $this->score > 200 => 8,
            $this->score > 100 => 7,
            default => 5,
        };

        $masks = match (true) {
            // TODO: Options with less tiles
            $this->score > 700 => [
                '1 1 1 1 1 1 1 1 1 1 1 1 1 1 1 1',
                '0 1 1 1 1 1 1 4 4 1 1 1 1 1 1 0',
                '1 2 0 1 3 2 1 0 0 1 2 3 1 0 2 1',
                '1 1 2 2 1 1 3 3 3 3 1 1 2 2 1 1',
                '1 2 3 4 3 2 1 2 3 4 3 2 1 2 3 4',
            ],
            $this->score > 550 => [
                '0 1 1 1 1 1 1 1 1 1 1 1 1 1 1 0',
                '2 1 2 1 2 1 2 1 2 1 2 1 2 1 2 1',
                '0 1 1 1 2 2 3 3 3 3 2 2 1 1 1 0',
                '0 0 1 1 1 2 2 3 3 2 2 1 1 1 0 0',
            ],
            $this->score > 400 => [
                '0 0 1 1 1 1 1 1 1 1 1 1 1 1 0 0',
                '0 0 2 1 1 1 3 0 0 3 1 1 1 2 0 0',
                '0 0 1 1 2 2 3 3 3 3 2 2 1 1 0 0',
                '0 0 1 0 0 2 3 3 3 3 2 0 0 1 0 0',
            ],
            $this->score > 250 => [
                '0 0 0 1 1 1 2 0 0 2 1 1 1 0 0 0',
                '0 0 0 0 1 2 2 3 3 2 2 1 0 0 0 0',
                '0 0 0 1 1 2 2 3 3 2 2 1 1 0 0 0',
                '0 0 0 1 1 2 2 2 2 2 2 1 1 0 0 0',
            ],
            $this->score > 150 => [
                '0 0 0 0 1 1 1 1 1 1 1 1 0 0 0 0',
                '0 0 0 0 0 2 1 2 2 1 2 0 0 0 0 0',
                '0 0 0 0 0 1 1 2 2 1 1 0 0 0 0 0',
                '0 0 0 0 1 0 1 2 2 1 0 1 0 0 0 0',
            ],
            $this->score > 50 => [
                '0 0 0 0 0 1 1 1 1 1 1 0 0 0 0 0',
                '0 0 0 0 0 0 1 2 2 1 0 0 0 0 0 0',
                '0 0 0 0 0 0 1 1 1 1 0 0 0 0 0 0',
                '0 0 0 0 0 2 1 0 0 1 2 0 0 0 0 0',
            ],
            default => [
                '0 0 0 0 0 0 1 1 1 1 0 0 0 0 0 0',
                '0 0 0 0 0 0 1 2 2 1 0 0 0 0 0 0',
            ],
        };

        $maskAsString = $masks[array_rand($masks)];

        $mask = collect(explode(' ', $maskAsString));

        $newTileCount = $mask->sum();

        $pairCount = $newTileCount / $pairs;

        $values = [];

        foreach (range(1, $newTileCount) as $i) {
            $value = random_int(1, $maxValue);

            foreach (range(1, $pairCount) as $j) {
                $values[] = $value;
            }
        }

        shuffle($values);

        return $mask
            ->map(function (int $count, int $x) use (&$values, $maxValue) {
                if ($count === 0) {
                    return [];
                }

                return collect(range(0, $count - 1))
                    ->map(function (int $z) use ($x, &$values) {
                        $tile = new Tile(
                            x: $x,
                            y: 1,
                            z: $z,
                            value: current($values),
                            board: $this
                        );

                        next($values);

                        return $tile;
                    })
                    ->toArray();
            })
            ->toArray();
    }

    private function isFullEmptyRow(array $row): bool
    {
        foreach ($row as $tiles) {
            if ($tiles !== []) {
                return false;
            }
        }

        return true;
    }

    public function canSelect(Tile $tile): bool
    {
        if ($tile->isFound()) {
            return false;
        }

        $left = $tile->getLeft();
        $right = $tile->getRight();

        if ($left === null || $right === null) {
            return true;
        }

        if ($left->isFound() || $right->isFound()) {
            return true;
        }

        return false;
    }

    public function findSelected(): ?Tile
    {
        foreach ($this->loop() as $tile) {
            if ($tile && $tile->isSelected()) {
                return $tile;
            }
        }

        return null;
    }

    public function findMatching(Tile $tile): ?Tile
    {
        foreach ($this->loop() as $other) {
            if ($other->isOpen() && $tile->matchesWith($other)) {
                return $other;
            }
        }

        return null;
    }

    public function hasOpenMatch(Tile $tile): bool
    {
        foreach ($this->loop() as $matchingTile) {
            if (
                $tile->valueEquals($matchingTile)
                && $this->canSelect($tile)
                && $this->canSelect($matchingTile)
            ) {
                return true;
            }
        }

        return false;
    }

    public function isDone(): bool
    {
        return iterator_count($this->loop()) === 0;
    }

    public function shuffle(): self
    {
        $tiles = iterator_to_array($this->loop());

        shuffle($tiles);

        foreach ($this->tiles as $y => $row) {
            foreach ($row as $x => $tiles) {
                foreach ($tiles as $z => $tile) {
                    /** @var \App\Http\Livewire\Tile $newTile */
                    $newTile = current($tiles);

                    $this->tiles[$y][$x][$z] = $newTile->move($x, $y, $z);

                    $newTile->markUnselected();

                    next($tiles);
                }
            }
        }

        return $this;
}

    private function loop(): Generator
    {
        foreach ($this->tiles as $row) {
            foreach ($row as $tiles) {
                foreach ($tiles as $tile) {
                    yield $tile;
                }
            }
        }
    }
}
