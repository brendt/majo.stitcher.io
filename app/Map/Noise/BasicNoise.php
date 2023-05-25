<?php

namespace App\Map\Noise;

use App\Map\Point;

final class BasicNoise implements Noise
{
    public function __construct(private int $seed) {}

    public function generate(int $x, int $y, int $iterations = 64): float|int
    {
        $point = new Point($x, $y);

        $topLeftPoint = $point->move(
            x: (floor(($point->x - 5) / 10) * 10) + 5,
            y: (floor(($point->y - 5) / 10) * 10) + 5,
        );

        $topRightPoint = $point->move(
            x: (ceil(($point->x - 5) / 10) * 10) + 5,
            y: (floor(($point->y - 5) / 10) * 10) + 5,
        );

        $bottomLeftPoint = $point->move(
            x: (floor(($point->x - 5) / 10) * 10) + 5,
            y: (ceil(($point->y - 5) / 10) * 10) + 5
        );

        $bottomRightPoint = $point->move(
            x: (ceil(($point->x - 5) / 10) * 10) + 5,
            y: (ceil(($point->y - 5) / 10) * 10) + 5
        );

        $lerp = Lerp::SMOOTHSTEP;

        if (($point->x + 5) % 10 === 0 && ($point->y + 5) % 10 === 0) {
            $noise = $this->hash($point);
        } elseif ($topRightPoint->x - $topLeftPoint->x === 0) {
            $noise = $lerp->generate(
                $this->hash($topLeftPoint),
                $this->hash($bottomRightPoint),
                ($point->y - $topLeftPoint->y) / ($bottomRightPoint->y - $topLeftPoint->y),
            );
        } elseif ($bottomLeftPoint->y - $topLeftPoint->y === 0) {
            $noise = $lerp->generate(
                $this->hash($topLeftPoint),
                $this->hash($bottomRightPoint),
                ($point->x - $topLeftPoint->x) / ($bottomRightPoint->x - $topLeftPoint->x),
            );
        } else {
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
