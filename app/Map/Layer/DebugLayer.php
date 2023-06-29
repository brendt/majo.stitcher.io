<?php

namespace App\Map\Layer;

use App\Map\Noise\Noise;
use App\Map\Point;
use App\Map\Tile\GenericTile\DebugTile;
use App\Map\Tile\GenericTile\LandTile;
use App\Map\Tile\Tile;
use App\Map\Vector;

final readonly class DebugLayer
{
    private int $seed;

    public function __construct(
        private Noise $noise,
    )
    {
        $this->seed = request()->get('seed', 123);
    }

    public function __invoke(Tile $tile, BaseLayer $base): Tile
    {
//        $input = $this->seed * $tile->getPoint()->x * $tile->getPoint()->y;
//
//        $noise = sin($input);

        return new DebugTile(
            point: $tile->getPoint(),
            noise: $this->noise->generate($tile->getPoint()),
            debug: [
                'x' => $tile->getPoint()->x,
                'y' => $tile->getPoint()->y,
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
