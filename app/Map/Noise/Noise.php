<?php

namespace App\Map\Noise;

interface Noise
{
    public function noise($x, $y, $z, $size = null): float|int;
}
