<?php

namespace App\Map\Noise;

use App\Map\Point;

final class BasicNoise implements Noise
{
    public function __construct(private int $seed) {}

    public function generate(int $x, int $y, int $iterations = 64): float|int
    {
        $point = new Point($x, $y);

        $lerp = Lerp::SMOOTHSTEP;

        if (($point->x) % 10 === 0 && ($point->y) % 10 === 0) {
            $noise = $this->hash($point);
        }
        elseif ($point->x % 10 === 0) {
            $topPoint = $point->move(
                y: (floor($point->y / 10) * 10),
            );

            $bottomPoint = $point->move(
                y: (ceil($point->y / 10) * 10)
            );

            $noise = $lerp->generate(
                $this->hash($topPoint),
                $this->hash($bottomPoint),
                ($point->y - $topPoint->y) / ($bottomPoint->y - $topPoint->y),
            );
        }
        elseif ($point->y % 10 === 0) {
            $leftPoint = $point->move(
                x: (floor($point->x / 10) * 10),
            );

            $rightPoint = $point->move(
                x: (ceil($point->x / 10) * 10),
            );

            $noise = $lerp->generate(
                $this->hash($leftPoint),
                $this->hash($rightPoint),
                ($point->x - $leftPoint->x) / ($rightPoint->x - $leftPoint->x),
            );
        } else {
            $topLeftPoint = $point->move(
                x: (floor(($point->x) / 10) * 10),
                y: (floor(($point->y) / 10) * 10),
            );

            $topRightPoint = $point->move(
                x: (ceil(($point->x) / 10) * 10),
                y: (floor(($point->y) / 10) * 10),
            );

            $bottomLeftPoint = $point->move(
                x: (floor(($point->x) / 10) * 10),
                y: (ceil(($point->y) / 10) * 10),
            );

            $bottomRightPoint = $point->move(
                x: (ceil(($point->x) / 10) * 10),
                y: (ceil(($point->y) / 10) * 10),
            );

            $a = $lerp->generate(
                $this->hash($topLeftPoint),
                $this->hash($topRightPoint),
                ($point->x - $topLeftPoint->x) / ($topRightPoint->x - $topLeftPoint->x),
            );

            $b = $lerp->generate(
                $this->hash($bottomLeftPoint),
                $this->hash($bottomRightPoint),
                ($point->x - $bottomLeftPoint->x) / ($bottomRightPoint->x - $bottomLeftPoint->x),
            );

            $noise = $lerp->generate(
                $a,
                $b,
                ($point->y - $topLeftPoint->y) / ($bottomLeftPoint->y - $topLeftPoint->y),
            );
        }

        return $noise;
    }

    private function hash(Point $point): float
    {
        $baseX = ceil($point->x / 10);
        $baseY = ceil($point->y / 10);

        $hash = bin2hex(
                hash(
                    algo: 'xxh32',
                    data: $this->seed * $baseX * $baseY,
                )
            );

        $hash = floatval('0.' . $hash);

        return sqrt($hash);
    }
}
