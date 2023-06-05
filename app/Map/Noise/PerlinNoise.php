<?php

namespace App\Map\Noise;

final class PerlinNoise implements Noise
{
    public function generate(int $x, int $y, int $iterations = 64): float|int {}
}
