<?php

namespace App\Map\Noise;

use App\Map\Point;

interface Noise
{
    public function generate(Point $point, int $iterations = 64): float|int;
}
