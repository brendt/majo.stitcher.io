<?php

namespace App\Map\Noise;

use App\Map\Point;

final class MountainNoise implements Noise
{
    public function __construct(private int $seed) {}

    public function generate(int $x, int $y, int $iterations = 64): float|int
    {
        $point = new Point($x, $y);



        return $noise;
    }
}
