<?php

namespace App\Game;

use Exception;
use Generator;
use Illuminate\Support\Facades\Session;

/*
 * TODO:
 *  - Fireworks
 *  - Short version
 *  - Keyboard shortcuts
 *  - Improved styling
 *  - Max amount of hints and shuffles
 */

/** @property \App\Game\Tile[][][] $tiles */
final class Board
{
    public function __construct(
        public array $tiles = [],
    ) {}

    public static function resolve(): ?self
    {
        return Session::get('board');
    }

    public function persist(): void
    {
        Session::put('board', $this);
    }

    public function destroy(): void
    {
        Session::remove('board');
    }

    public static function init(): self
    {
        $board = new Board();

        $maskInput = "
1 1 1 1 1 1 1 1 1 1
0 0 1 2 3 3 2 1 0 0
0 1 2 3 4 4 3 2 1 0
0 1 2 3 4 4 3 2 1 0
2 1 2 3 4 4 3 2 1 2
0 1 2 3 4 4 3 2 1 0
0 1 2 3 4 4 3 2 1 0
0 0 1 2 3 3 2 1 0 0
1 1 1 1 1 1 1 1 1 1
";

        // Base mask
        $mask = collect(explode(PHP_EOL, trim($maskInput)))
            ->map(fn (string $line) => collect(explode(' ', trim($line)))
                ->map(fn (string $height) => array_fill(0, $height, true))
            );

        // Create values array
        if ($mask->flatten()->sum() % 4 !== 0) {
            throw new Exception("Invalid mask: sum of tiles must always be divisible by 4");
        }

        $pairCount = $mask->flatten()->sum() / 4;

        $values = [];

        foreach (range(1, $pairCount) as $value) {
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
        }

        shuffle($values);

        // Fill the board
        foreach ($mask as $y => $column) {
            foreach ($column as $x => $row) {
                foreach ($row as $z => $true) {
                    $board->add(new Tile(
                        x: $x,
                        y: $y,
                        z: $z,
                        value: current($values),
                    ));

                    next($values);
                }
            }
        }

        return $board;
    }

    private function add(Tile $tile): void
    {
        $this->tiles[$tile->x][$tile->y][$tile->z] = $tile;
    }

    public function getXCount(): int
    {
        return count($this->tiles);
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

    public function remove(Tile $tile): void
    {
        unset($this->tiles[$tile->x][$tile->y][$tile->z]);
    }

    public function removePair(Tile $a, Tile $b): void
    {
        $this->remove($a);
        $this->remove($b);
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

    public function isFinished(): bool
    {
        return $this->getTileCount() === 0;
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
}
