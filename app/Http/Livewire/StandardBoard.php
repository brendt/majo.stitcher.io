<?php

namespace App\Http\Livewire;

use Exception;
use Generator;
use Session;

/** @property \App\Http\Livewire\Tile[][][] $tiles */
final class StandardBoard implements Board
{
    public function __construct(
        public readonly int $xCount,
        public readonly int $yCount,
        public readonly int $zCount,
        public array $tiles = [],
        public int $availableShuffles = 5,
        public ?int $seed = null,
    ) {
        if ($this->seed === null) {
            $this->seed = random_int(0, 100_000_000);
        }
    }

    public static function resolve(): ?self
    {
        return Session::get('board');
    }

    public static function init(?int $seed = null): self
    {
        if ($board = self::resolve()) {
            return $board;
        }

        $values = [];

//        $mask = "
//1 1 1 1
//";

        $mask = "
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

        $mask = collect(explode(PHP_EOL, trim($mask)));

        $map = $mask
            ->map(fn (string $line) => collect(explode(' ', trim($line)))
                ->map(fn (string $height) => array_fill(0, $height, true))
            );

        $xCount = $mask
            ->map(fn (string $line) => collect(explode(' ', trim($line)))->count())
            ->max();

        $yCount = $mask->count();

        $zCount = $mask
            ->flatMap(fn (string $line) => explode(' ', trim($line)))
            ->map(fn (string $item) => (int) $item)
            ->max();

        $itemCount = $map->flatten()->count() / 4;

        if ($map->flatten()->count() % 4 !== 0) {
            throw new Exception("Invalid tile count");
        }

        foreach (range(1, $itemCount) as $value) {
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
            $values[] = $value;
        }

        $board = new StandardBoard(
            xCount: $xCount,
            yCount: $yCount,
            zCount: $zCount,
            seed: $seed,
        );

        $values = $board->shuffleWithSeed($values);

        foreach (range(0, $xCount - 1) as $x) {
            foreach (range(0, $yCount - 1) as $y) {
                foreach (range(0, $zCount - 1) as $z) {
                    if (! isset($map[$y][$x][$z])) {
                        continue;
                    }

                    $board->add(new Tile(
                        x: $x,
                        y: $y,
                        z: $z,
                        value: current($values),
                        board: $board,
                    ));

                    next($values);
                }
            }
        }

        $board->persist();

        return $board;
    }

    public function shuffle(): self
    {
        $tiles = iterator_to_array($this->loop());

        shuffle($tiles);

        foreach ($this->tiles as $x => $layer) {
            foreach ($layer as $y => $row) {
                foreach ($row as $z => $oldTile) {
                    /** @var \App\Http\Livewire\Tile $newTile */
                    $newTile = current($tiles);

                    $this->tiles[$x][$y][$z] = $newTile->move($x, $y, $z);

                    $newTile->markUnselected();

                    next($tiles);
                }
            }
        }

        $this->availableShuffles -= 1;

        return $this;
    }

    public function reset(?int $seed = null): self
    {
        Session::remove('board');

        return self::init($seed);
    }

    public function showHint(): self
    {
        $withOpenMatch = null;

        foreach ($this->loop() as $tile) {
            $tile->markUnselected();
        }

        foreach ($this->loop() as $tile) {
            if ($tile->hasOpenMatch()) {
                $withOpenMatch = $tile;

                break;
            }
        }

        $matching = $this->findMatching($withOpenMatch);

        $matching->markSelected();
        $withOpenMatch->markSelected();

        return $this;
    }

    public function add(Tile $tile): self
    {
        $this->tiles[$tile->x][$tile->y][$tile->z] = $tile;

        return $this;
    }

    public function remove(Tile $tile): static
    {
        unset($this->tiles[$tile->x][$tile->y][$tile->z]);

        return $this;
    }

    public function get(int $x, int $y, ?int $z = null): ?Tile
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

    public function canSelect(Tile $tile): bool
    {
        if ($tile->isFound()) {
            return false;
        }

        if (! $tile->isOntop()) {
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
            if ($tile->isSelected()) {
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

    public function persist(): void
    {
        Session::put('board', $this);
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

    public function getAvailablePairs(): int
    {
        $available = 0;

        foreach ($this->loop() as $tile) {
            $available += $tile->hasOpenMatch() ? 1 : 0;
        }

        return $available / 2;
    }

    public function getAvailableShuffles(): int
    {
        return $this->availableShuffles;
    }

    public function isDone(): bool
    {
        return iterator_count($this->loop()) === 0;
    }

    private function loop(): Generator
    {
        foreach ($this->tiles as $layer) {
            foreach ($layer as $row) {
                foreach ($row as $tile) {
                    yield $tile;
                }
            }
        }
    }

    private function shuffleWithSeed(array $input): array
    {
        mt_srand($this->seed);

        $order = array_map(
            fn ($val) => mt_rand(),
            range(1, count($input))
        );

        array_multisort($order, $input);

        return $input;
    }
}
