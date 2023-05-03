<?php

namespace App\Map\Noise;

use BlackScorp\SimplexNoise\Noise2D;

final class SimplexGenerator implements Noise
{
    public function __construct(
        private $noise = new Noise2D()
    ) {}

    public function noise($x, $y, $z, $size = null): float|int
    {
        return $this->noise->getGreyValue($x, $y);
    }
}
