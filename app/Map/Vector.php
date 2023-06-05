<?php

namespace App\Map;

final readonly class Vector
{
    public function __construct(
        public float $a,
        public float $b,
    ) {}

    public function dot(Vector $other): float
    {
        return ($this->a * $other->a) + ($this->b * $other->b);
    }

    public function __toString(): string
    {
        return "($this->a, $this->b)";
    }
}
