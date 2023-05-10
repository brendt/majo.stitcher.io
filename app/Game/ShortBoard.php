<?php

namespace App\Game;

use Generator;
use Illuminate\Support\Facades\Session;

/** @property \App\Game\Tile[][][] $tiles */
final class ShortBoard
{
    private const HEIGHT = 5;

    private int $score = 0;

    public function __construct(
        public array $tiles = [],
    ) {}

    public static function resolve(): ?self
    {
        return Session::get('short-board');
    }

    public function persist(): void
    {
        Session::put('short-board', $this);
    }

    public function destroy(): void
    {
        Session::remove('short-board');
    }

    public static function init(): self
    {
        $board = new ShortBoard();

        $values = [];

        foreach (range(1, self::HEIGHT) as $value) {
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
        }

        shuffle($values);

        foreach (range(0, 1) as $x) {
            foreach (range(0, self::HEIGHT - 1) as $y) {
                $board->add(new Tile(
                    x: $x + 7,
                    y: $y,
                    z: 0,
                    value: current($values),
                ));

                next($values);
            }
        }

        return $board;
    }

    public function handleEmptyRows(): self
    {
        $rows = [];

        foreach ($this->loop() as $tile) {
            $rows[$tile->y][$tile->x][$tile->z] = $tile;
        }

        while (count($rows) < self::HEIGHT) {
            array_unshift($rows, $this->createNewRow());

            $this->score += 20;
        }

        $newTiles = [];

        foreach ($rows as $y => $row) {
            foreach ($row as $x => $column) {
                foreach ($column as $z => $tile) {
                    $newTiles[$x][$y][$z] = $tile->move($x, $y, $z);
                }
            }
        }

        $this->tiles = $newTiles;

        return $this;
    }

    private function createNewRow(): array
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
                        );

                        next($values);

                        return $tile;
                    })
                    ->toArray();
            })
            ->toArray();
    }

    public function getScore(): int
    {
        return $this->score;
    }

    private function add(Tile $tile): void
    {
        $this->tiles[$tile->x][$tile->y][$tile->z] = $tile;
    }

    public function get($x, $y, $z = null): ?Tile
    {
        if ($z === null) {
            $cell = $this->tiles[$x][$y] ?? [];

            if ($cell === []) {
                return null;
            }

            $z = max(array_keys($cell)) ?? null;
        }

        return $this->tiles[$x][$y][$z] ?? null;
    }

    public function getXCount(): int
    {
        return 16;
    }

    public function getAvailablePairs(): int
    {
        $availablePairs = 0;

        foreach ($this->loop() as $tile) {
            $availablePairs += $this->hasOpenMatch($tile) ? 1 : 0;
        }

        return $availablePairs / 2;
    }

    public function getTileCount(): int
    {
        return iterator_count($this->loop());
    }

    public function canSelect(Tile $tile): bool
    {
        $isOnTop = $this->get($tile->x, $tile->y)->isSame($tile);

        if (! $isOnTop) {
            return false;
        }

        $tileOnLeft = $this->get($tile->x - 1, $tile->y, $tile->z);

        if ($tileOnLeft === null) {
            return true;
        }

        $tileOnRight = $this->get($tile->x + 1, $tile->y, $tile->z);

        if ($tileOnRight === null) {
            return true;
        }

        return false;
    }

    public function getCurrentlySelectedTile(): ?Tile
    {
        foreach ($this->loop() as $tile) {
            if ($tile->isSelected()) {
                return $tile;
            }
        }

        return null;
    }

    public function clearSelectedTiles(): void
    {
        foreach ($this->loop() as $tile) {
            $tile->markUnselected();
        }
    }

    public function remove(Tile $tile): void
    {
        unset($this->tiles[$tile->x][$tile->y][$tile->z]);

        $this->score += (int) $tile->value;
    }

    public function removePair(Tile $a, Tile $b): void
    {
        $this->remove($a);
        $this->remove($b);
        $this->handleEmptyRows();
    }

    private function hasOpenMatch(Tile $tile): bool
    {
        foreach ($this->loop() as $matchingTile) {
            if (
                $tile->matches($matchingTile)
                && $this->canSelect($tile)
                && $this->canSelect($matchingTile)
            ) {
                return true;
            }
        }

        return false;
    }

    public function isFinished(): bool
    {
        return $this->getTileCount() === 0;
    }

    /**
     * @return \App\Game\Tile[]
     */
    public function findHighlightedTiles(): array
    {
        $highlightedTiles = [];

        foreach ($this->loop() as $tile) {
            if ($tile->isHighlighted()) {
                $highlightedTiles[] = $tile;
            }
        }

        return $highlightedTiles;
    }

    public function isStuck(): bool
    {
        return $this->getTileCount() > 0
            && $this->getAvailablePairs() === 0;
    }

    public function showHint(): self
    {
        $this->clearSelectedTiles();

        $tileWithOpenMatch = null;

        foreach ($this->loop() as $tile) {
            if ($this->hasOpenMatch($tile)) {
                $tileWithOpenMatch = $tile;
                break;
            }
        }

        $matchingTile = null;

        foreach ($this->loop() as $tile) {
            if (
                $this->canSelect($tile)
                && $tile->matches($tileWithOpenMatch)
            ) {
                $matchingTile = $tile;
                break;
            }
        }

        $tileWithOpenMatch->state = TileState::HIGHLIGHTED;
        $matchingTile->state = TileState::HIGHLIGHTED;

        return $this;
    }

    public function shuffle(): self
    {
        $tiles = iterator_to_array($this->loop());

        shuffle($tiles);

        foreach ($this->tiles as $x => $row) {
            foreach ($row as $y => $column) {
                foreach ($column as $z => $tile) {
                    /** @var \App\Game\Tile $newTile */
                    $newTile = current($tiles);

                    $this->tiles[$x][$y][$z] = $newTile->move($x, $y, $z);

                    next($tiles);
                }
            }
        }

        $this->clearSelectedTiles();

        return $this;
    }

    /**
     * @return \Generator|\App\Game\Tile[]
     */
    private function loop(): Generator
    {
        foreach ($this->tiles as $row) {
            foreach ($row as $column) {
                foreach ($column as $tile) {
                    yield $tile;
                }
            }
        }
    }
}
