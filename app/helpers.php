<?php

function hex(float $value): string
{
    if ($value > 1.0) {
        $value = 1.0;
    }

    $hex = dechex((int) ($value * 255));

    if (strlen($hex) < 2) {
        $hex = "0" . $hex;
    }

    return $hex;
}

function lerp(float $a, float $b, float $fraction): float
{

    return $a + $fraction * ($b - $a);
}

function smooth(float $a, float $b, float $fraction): float
{
    $smooth = function (float $fraction): float {
        $v1 = $fraction * $fraction;
        $v2 = 1.0  - (1.0 - $fraction) * (1.0 -$fraction);

        return lerp($v1, $v2, $fraction);
    };

//    $smooth = fn ($f) => $f;

    return lerp($a, $b, $smooth($fraction));
}
