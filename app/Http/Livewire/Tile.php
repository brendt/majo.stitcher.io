<?php

namespace App\Http\Livewire;

use Spatie\Cloneable\Cloneable;

final class Tile
{
    use Cloneable;

    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
        public readonly string $value,
        private readonly Board $board,
        private bool $isSelected = false,
        private bool $isFound = false,
    ) {}

    public function __toString(): string
    {
        return "{$this->x}, {$this->y}, {$this->z}";
    }

    public function getState(): TileState
    {
        return match (true) {
            $this->isFound() => TileState::FOUND,
            $this->isSelected() => TileState::SELECTED,
            $this->hasOpenMatch() => TileState::OPEN_WITH_MATCH,
            $this->isOpen() => TileState::OPEN,
            default => TileState::CLOSED,
        };
    }

    public function move(int $x, int $y, int $z): Tile
    {
        return $this->with(
            x: $x,
            y: $y,
            z: $z,
        );
    }

    public function isOpen(): bool
    {
        return $this->board->canSelect($this);
    }

    public function markAsFound(): void
    {
        $this->isFound = true;
        $this->isSelected = false;
        $this->board->remove($this);
    }

    public function markUnselected(): void
    {
        $this->isSelected = false;
    }

    public function markSelected(): void
    {
        $this->isSelected = true;
    }

    public function isFound(): bool
    {
        return $this->isFound;
    }

    public function isOnTop(): bool
    {
        return $this->board->get($this->x, $this->y)->isSame($this);
    }

    public function getLeft(): ?self
    {
        return $this->board->get($this->x - 1, $this->y, $this->z);
    }

    public function getRight(): ?self
    {
        return $this->board->get($this->x + 1, $this->y, $this->z);
    }

    public function isSelected(): bool
    {
        return $this->isSelected;
    }

    public function hasOpenMatch(): bool
    {
        return $this->board->hasOpenMatch($this);
    }

    public function isSame(?self $other): bool
    {
        return $this->x === $other->x
            && $this->y === $other->y
            && $this->z === $other->z;
    }

    public function valueEquals(?self $other): bool
    {
        if ($other === null) {
            return false;
        }

        if ($this->isSame($other)) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function valueNotEquals(?self $other): bool
    {
        if ($other === null) {
            return false;
        }

        if ($this->isSame($other)) {
            return false;
        }

        return $this->value !== $other->value;
    }

    public function toggleSelected(): void
    {
        $this->isSelected = ! $this->isSelected;
    }

    public function getColor(): string
    {
        if ($this->value === '') {
            return '';
        }

        return match ((int) floor($this->value / 10)) {
            0 => '#E8E3D3',
            1 => '#D6E6B8',
            2 => '#E6CFB8',
            default => '#B8D2E6',
        };
    }

    public function toArray(): array
    {
        return (array) $this;
    }

    public function matchesWith(self $other): bool
    {
        return $this->valueEquals($other)
            && ! $this->isSame($other);
    }
}
