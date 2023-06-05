<?php

namespace App\Map\Layer;

use App\Map\Noise\Lerp;
use App\Map\Noise\Noise;
use App\Map\Point;
use App\Map\Tile\GenericTile\DebugTile;
use App\Map\Tile\Tile;
use App\Map\Vector;

final readonly class DebugLayer implements Layer
{
    private int $seed;

    public function __construct(
        private Noise $noise,
    )
    {
        $this->seed = request()->get('seed', 123);
    }

    public function generate(Tile $tile, BaseLayer $base): Tile
    {
        $point = $tile->getPoint();

        $x0 = floor($point->x / 10) * 10;
        $x1 = $x0 + 10;
        $y0 = floor($point->y / 10) * 10;
        $y1 = $y0 + 10;

        $fractionX = ($point->x - $x0) / ($x1 - $x0);
        $fractionY = ($point->y - $y0) / ($y1 - $y0);

        $n0 = $this->dotGradient(
            new Point($x0, $y0),
            $point,
        );

        $n1 = $this->dotGradient(
            new Point($x1, $y0),
            $point,
        );

        $ix0 = Lerp::DEFAULT->generate($n0, $n1, $fractionX);

        $n0 = $this->dotGradient(
            new Point($x0, $y1),
            $point,
        );

        $n1 = $this->dotGradient(
            new Point($x1, $y1),
            $point,
        );

        $ix1 = Lerp::DEFAULT->generate($n0, $n1, $fractionX);

        $noise = abs(Lerp::DEFAULT->generate($ix0, $ix1, $fractionY));

        return new DebugTile(
            x: $tile->getX(),
            y: $tile->getY(),
            noise: $noise,
            debug: [
                'grad' => $this->gradient($point),
                'x0' => $x0,
                'y0' => $y0,
                'x1' => $x1,
                'y1' => $y1,
                'fractionX' => $fractionX,
                'fractionY' => $fractionY,
            ]
        );
    }

    private function dotGradient(Point $a, Point $b): float
    {
        $gradient = $this->gradient($a);

        $distanceX = $b->x - $a->x;
        $distanceY = $b->y - $a->y;

        return ($distanceX * $gradient->a) + ($distanceY * $gradient->b);
    }

    private function gradient(Point $point): Vector
    {
        $input = $this->seed * $point->x * $point->y;

        $a = bin2hex(hash(
            algo: 'xxh32',
            data: $input,
        ));

        $b = bin2hex(hash(
            algo: 'xxh32',
            data: $input * 2,
        ));

        $base = 9999999999999999;

        return new Vector(
            $a / $base,
            $b / $base,
        );
    }
}
