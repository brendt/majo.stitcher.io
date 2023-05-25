<?php

namespace App\Map\Noise;

use Closure;

enum Lerp
{
    case DEFAULT;
    case SMOOTHSTEP;
    case TRIANGLE;

    public function generate(
        float $a,
        float $b,
        float $fraction,
        ?Closure $shape = null
    ): float
    {
        $shape ??= $this->shape(...);

        return $a + $shape($fraction) * ($b - $a);
    }

    private function shape(float $fraction): float
    {
        return match ($this) {
            self::TRIANGLE => 1.0 - 2.0 * abs($fraction - 0.5),
            self::SMOOTHSTEP => (function (float $fraction) {
                $v1 = $fraction * $fraction;
                $v2 = 1.0  - (1.0 - $fraction) * (1.0 -$fraction);

                return self::DEFAULT->generate($v1, $v2, $fraction);
            })($fraction),
            default => $fraction,
        };
    }
}
