<?php

namespace App\Map\Noise;

final class Perlin2Generator implements Noise
{
    private const HASH = [208, 34, 231, 213, 32, 248, 233, 56, 161, 78, 24, 140, 71, 48, 140, 254, 245, 255, 247, 247, 40,
        185, 248, 251, 245, 28, 124, 204, 204, 76, 36, 1, 107, 28, 234, 163, 202, 224, 245, 128, 167, 204,
        9, 92, 217, 54, 239, 174, 173, 102, 193, 189, 190, 121, 100, 108, 167, 44, 43, 77, 180, 204, 8, 81,
        70, 223, 11, 38, 24, 254, 210, 210, 177, 32, 81, 195, 243, 125, 8, 169, 112, 32, 97, 53, 195, 13,
        203, 9, 47, 104, 125, 117, 114, 124, 165, 203, 181, 235, 193, 206, 70, 180, 174, 0, 167, 181, 41,
        164, 30, 116, 127, 198, 245, 146, 87, 224, 149, 206, 57, 4, 192, 210, 65, 210, 129, 240, 178, 105,
        228, 108, 245, 148, 140, 40, 35, 195, 38, 58, 65, 207, 215, 253, 65, 85, 208, 76, 62, 3, 237, 55, 89,
        232, 50, 217, 64, 244, 157, 199, 121, 252, 90, 17, 212, 203, 149, 152, 140, 187, 234, 177, 73, 174,
        193, 100, 192, 143, 97, 53, 145, 135, 19, 103, 13, 90, 135, 151, 199, 91, 239, 247, 33, 39, 145,
        101, 120, 99, 3, 186, 86, 99, 41, 237, 203, 111, 79, 220, 135, 158, 42, 30, 154, 120, 67, 87, 167,
        135, 176, 183, 191, 253, 115, 184, 21, 233, 58, 129, 233, 142, 39, 128, 211, 118, 137, 139, 255,
        114, 20, 218, 113, 154, 27, 127, 246, 250, 1, 8, 198, 250, 209, 92, 222, 173, 21, 88, 102, 219];

    const FREQ = 20.0;

    public function __construct(private int $seed, private int $width, private int $height) {}

    public function generate(
        int $x,
        int $y,
        int $iterations = 10,
    ): float {
        $x = $x / $this->width;
        $y = $y / $this->height;

        $xa = $x * self::FREQ;
        $ya = $y * self::FREQ;
        $amp = 1.0;
        $fin = 0;
        $div = 0.0;

        for ($i = 0; $i < $iterations; $i++) {
            $div += 256 * $amp;
            $fin += $this->noise2d($xa, $ya) * $amp;
            $amp /= 2;
            $xa *= 2;
            $ya *= 2;
        }

        return $fin / $div;
    }

    public function noise2(int $x, int $y): int
    {
        $tmp = self::HASH[abs(($y + $this->seed) % 256)];

        return self::HASH[abs(($tmp + $x) % 256)];
    }

    private function noise2d(float $x, float $y) : float
    {
        $xInt = (int) $x;
        $yInt = (int) $y;
        $xFrac = $x - $xInt;
        $yFrac = $y - $yInt;

        $s = $this->noise2($xInt, $yInt);
        $t = $this->noise2($xInt + 1, $yInt);
        $u = $this->noise2($xInt, $yInt + 1);
        $v = $this->noise2($xInt + 1, $yInt + 1);

        $low = $this->smoothInter($s, $t, $xFrac);
        $high = $this->smoothInter($u, $v, $xFrac);

        return $this->smoothInter($low, $high, $yFrac);
    }

    private function smoothInter(float $x, float $y, float $s): float
    {
        return $this->linInter($x, $y, $s * $s * (3-2*$s));
    }

    private function linInter(float $x, float $y, float $s): float
    {
        return $x + $s * ($y - $x);
    }
    /*




    int
    main(int argc, char *argv[])
    {
        int x, y;
        for(y=0; y<4000; y++)
            for(x=0; x<4000; x++)
                perlin2d(x, y, 0.1, 4);

        return 0;
    }
         */

}



