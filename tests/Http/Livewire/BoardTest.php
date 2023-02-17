<?php

namespace Tests\Http\Livewire;

use App\Http\Livewire\ShortBoard;
use App\Http\Livewire\Tile;
use PHPUnit\Framework\TestCase;
use Ramsey\Collection\Collection;

class BoardTest extends TestCase
{

    public function testInit()
    {

        $mask = "
1 2 2
3 4
";
        $mask = collect(explode(PHP_EOL, trim($mask)));

        $map = $mask
            ->map(fn (string $line) => collect(explode(' ', trim($line)))
                ->map(fn (string $height) => array_fill(0, $height, true))
            );

        dd($map->toArray());

        $maxHeight = $mask
            ->flatMap(fn(string $line) => explode(' ', trim($line)))
            ->map(fn (string $item) => (int) $item)
            ->max();

        $yCount = $mask->count();


        $xCount = $mask
            ->map(fn(string $line) => collect(explode(' ', trim($line)))->count())
            ->max();

        dd($xCount);

        $itemCount = $map->flatten()->count();

        dd($itemCount);

        dd($mask);

        ///
    }

    /** @test */
    public function testMaskMap()
    {
        $masks = [
            '0 2 1',
            '0 1 2',
        ];

        $mask = collect(explode(' ', $masks[array_rand($masks)]));

        $row = $mask
            ->map(function (int $count, int $x) {
                if ($count === 0) {
                    return [];
                }

                return collect(range(1, $count))
                    ->map(function ($z) use ($x) {
                        return new Tile(x: $x, y: 1, z: $z, value: 1, board: new ShortBoard());
                    })
                    ->toArray();
            });

        dd($mask, $row->toArray());
    }
}
