<?php

namespace App\Map\Noise;

interface Noise
{
    public function generate(int $x, int $y, int $iterations = 64): float|int;
}
