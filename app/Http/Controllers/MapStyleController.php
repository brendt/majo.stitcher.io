<?php

namespace App\Http\Controllers;

use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\FishLayer;
use App\Map\Layer\FlaxLayer;
use App\Map\Layer\GoldLayer;
use App\Map\Layer\IslandLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\DebugLayer;
use App\Map\Layer\MountainLayer;
use App\Map\Layer\StoneLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\ValleyLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\BasicNoise;
use App\Map\Noise\MountainNoise;
use App\Map\Noise\Perlin2Generator;
use App\Map\Noise\PerlinGenerator;
use App\Map\Noise\ScatterNoise;

final class MapStyleController
{
    private function drawPixel(int $x, int $y): string
    {
        return <<<HTML
    <div style="--x: {$x}; --y: {$y}"></div>
    HTML;
    }

    public function __invoke()
    {
        $pixels = [];

        for($x = 0; $x < 150; $x++) {
            for($y = 0; $y < 100; $y++) {
                $pixels[$x][$y] = $this->drawPixel($x, $y);
            }
        }

        return view('mapStyle', [
            'pixels' => $pixels,
        ]);
    }
}
