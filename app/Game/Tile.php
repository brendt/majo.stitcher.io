<?php

namespace App\Game;

final class Tile
{
    public function __construct(
        public readonly int $x,
        public readonly int $y,
        public readonly int $z,
        public readonly string $value,
        public TileState $state = TileState::DEFAULT,
    ) {}

    public function __toString(): string
    {
        return "{$this->x}, {$this->y}, {$this->z}";
    }

    public function move(int $x, int $y, int $z): self
    {
        return new self(
            x: $x,
            y: $y,
            z: $z,
            value: $this->value,
            state: $this->state,
        );
    }

    public function matches(Tile $other): bool
    {
        if ($this->isSame($other)) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function isSame(Tile $other): bool
    {
        return $this->x === $other->x
            && $this->y === $other->y
            && $this->z === $other->z;
    }

    public function isSelected(): bool
    {
        return $this->state === TileState::SELECTED;
    }

    public function isHighlighted(): bool
    {
        return $this->state === TileState::HIGHLIGHTED;
    }

    public function markUnselected(): void
    {
        $this->state = TileState::DEFAULT;
    }

    public function toggleSelected(): void
    {
        $this->state = $this->state === TileState::SELECTED
            ? TileState::DEFAULT
            : TileState::SELECTED;
    }

    public function getColor(): string
    {
        return match ((int) floor($this->value / 10 )) {
            0 => 'LightCoral',
            1 => 'MediumSeaGreen',
            2 => 'MediumSlateBlue',
            default => 'Orange',
        };
    }
}
