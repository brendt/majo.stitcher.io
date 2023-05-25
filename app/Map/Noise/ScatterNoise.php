<?php

namespace App\Map\Noise;

use App\Map\Point;

final readonly class ScatterNoise implements Noise
{
    public function __construct(
        private int $seed,
        private float $amunt = 1.0,
    ) {}

    public function amount(float $amount): self
    {
        return new self($this->seed, $amount);
    }

    public function generate(int $x, int $y, int $iterations = 64): float|int
    {
        $point = new Point($x, $y);

        $hash = $this->hash($point);

        $threshold = 1 - $this->amunt;

        return $hash < $threshold
            ? 0.0
            : $hash;
    }

    private function hash(Point $point): float
    {
        $hash = hexdec(
            hash(
                algo: 'xxh3',
                data: $this->seed * $point->x * $point->y,
            )
        );

        return floatval('0.' . $hash);
    }
}
