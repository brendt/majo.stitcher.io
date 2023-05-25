<?php

namespace App\Map;

final readonly class Point
{
    public function __construct(
        public int $x,
        public int $y,
    ) {}

    public function translate(int $x = 0, int $y = 0): self
    {
        return new Point($this->x + $x, $this->y + $y);
    }

    public function move(?int $x = null, ?int $y = null): self
    {
        return new Point($x ?? $this->x, $y ?? $this->y);
    }

    public function hash(string $seed): float
    {
    }

    public function distanceBetween(Point $other): float
    {
        return abs(
            sqrt(
                pow(
                    num: $other->x - $this->x,
                    exponent: 2
                ) + pow(
                    num: $other->y - $this->y,
                    exponent: 2
                )
            )
        );
    }

    public function relativeDistance(Point $a, Point $b): float
    {
        $totalDistance = $a->distanceBetween($b);

        $fractalDistance = $a->distanceBetween($this);

        return $fractalDistance / $totalDistance;
    }

    public function lerpBetween(Point $a, Point $b): float
    {
        $fraction = $this->relativeDistance($a, $b);

        $xLerp = ($a->x * $fraction) + ($this->x * (1 - $fraction));
        $yLerp = ($a->y * $fraction) + ($this->y * (1 - $fraction));

        return $xLerp * $yLerp;
    }

    public function __toString(): string
    {
        return "[{$this->x}, {$this->y}]";
    }
}
