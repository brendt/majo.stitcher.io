<?php

namespace App\Http\Controllers;

use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\IslandLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\DebugLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\BasicNoise;
use App\Map\Noise\Perlin2Generator;
use App\Map\Noise\PerlinGenerator;
use App\Map\Noise\ScatterNoise;
use App\Map\Noise\SimpleNoiseGenerator;

final class MapPreviewController
{
    public function __invoke()
    {
        $seed = request()->get('seed', random_int(1, 1_000_000));
        $perlin = new PerlinGenerator($seed);
        $basicNoise = new BasicNoise($seed);
        $perlin2 = new Perlin2Generator($seed, 100, 100);
        $scatterNoise = new ScatterNoise($seed);

        $baseLayer = (new BaseLayer(width: 150, height: 100))
//            ->add(new DebugLayer())
            ->add(new ElevationLayer($basicNoise))
            ->add(new IslandLayer())
            ->add(new BiomeLayer())
            ->add(new LandLayer())
            ->add(new WoodLayer($scatterNoise->amount(0.1)))
            ->generate();

        return view('mapPreview', [
            'board' => $baseLayer->getBoard(),
        ]);
    }
}
