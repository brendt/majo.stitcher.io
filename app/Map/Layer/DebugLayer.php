<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Point;
use App\Map\Tile\GenericTile\DebugTile;
use App\Map\Tile\Tile;

final readonly class DebugLayer implements Layer
{
    public function __construct(
        private Noise $noise,
    ) {}

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
//        return new DebugTile(
//            x: $tile->getX(),
//            y: $tile->getY(),
//            noise: $this->noise->generate($tile->getX(), $tile->getY()),
//        );

        $point = $tile->getPoint();

        return new DebugTile(
            x: $tile->getX(),
            y: $tile->getY(),
            noise: $this->baseNoise($point) * $this->circularNoise(150, 100, $point),
        );
    }

    private function circularNoise(int $totalWidth, int $totalHeight, Point $point): float
    {
        $middleX = $totalWidth / 2;
        $middleY = $totalHeight / 2;

        $distanceFromMiddle = sqrt(
            pow(($point->x - $middleX), 2)
            + pow(($point->y - $middleY), 2)
        );

        $maxDistanceFromMiddle = sqrt(
            pow(($totalWidth - $middleX), 2)
            + pow(($totalHeight - $middleY), 2)
        );

        return 1 - ($distanceFromMiddle / $maxDistanceFromMiddle) + 0.3;
    }

    private function hash(Point $point): float
    {
        $baseX = ceil($point->x / 10);
        $baseY = ceil($point->y / 10);

        $hash = bin2hex(
            hash(
                algo: 'xxh32',
                data: request()->get('seed', 123) * $baseX * $baseY,
            )
        );

        $hash = floatval('0.' . $hash);

        return sqrt($hash);
    }

    /**
     * @param Point $point
     * @return float
     */
    public function baseNoise(Point $point): float
    {
        $noise = 0;

        if ($point->x % 10 === 0 && $point->y % 10 === 0) {
            $noise = $this->hash($point);
        } elseif ($point->x % 10 === 0) {
            $topPoint = new Point(
                x: $point->x,
                y: (floor($point->y / 10) * 10),
            );

            $bottomPoint = new Point(
                x: $point->x,
                y: (ceil($point->y / 10) * 10)
            );

            $noise = smooth(
                $this->hash($topPoint),
                $this->hash($bottomPoint),
                ($point->y - $topPoint->y) / ($bottomPoint->y - $topPoint->y),
            );
        } elseif ($point->y % 10 === 0) {
            $leftPoint = new Point(
                x: (floor($point->x / 10) * 10),
                y: $point->y,
            );

            $rightPoint = new Point(
                x: (ceil($point->x / 10) * 10),
                y: $point->y,
            );

            $noise = smooth(
                $this->hash($leftPoint),
                $this->hash($rightPoint),
                ($point->x - $leftPoint->x) / ($rightPoint->x - $leftPoint->x),
            );
        } else {
            $topLeftPoint = new Point(
                x: (floor($point->x / 10) * 10),
                y: (floor($point->y / 10) * 10),
            );

            $topRightPoint = new Point(
                x: (ceil($point->x / 10) * 10),
                y: (floor($point->y / 10) * 10),
            );

            $bottomLeftPoint = new Point(
                x: (floor($point->x / 10) * 10),
                y: (ceil($point->y / 10) * 10)
            );

            $bottomRightPoint = new Point(
                x: (ceil($point->x / 10) * 10),
                y: (ceil($point->y / 10) * 10)
            );

            $a = smooth(
                $this->hash($topLeftPoint),
                $this->hash($topRightPoint),
                ($point->x - $topLeftPoint->x) / ($topRightPoint->x - $topLeftPoint->x),
            );

            $b = smooth(
                $this->hash($bottomLeftPoint),
                $this->hash($bottomRightPoint),
                ($point->x - $bottomLeftPoint->x) / ($bottomRightPoint->x - $bottomLeftPoint->x),
            );

            $noise = smooth(
                $a,
                $b,
                ($point->y - $topLeftPoint->y) / ($bottomLeftPoint->y - $topLeftPoint->y),
            );
        }
        return $noise;
    }
}
