<?php

namespace App\Http\Controllers;

use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\IslandLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\PerlinGenerator;

final class MapPreviewController
{
    public function __invoke()
    {
        $perlin = new PerlinGenerator(random_int(1, 1_000_000));
        $simplex = new PerlinGenerator(random_int(1, 1_000_000));

        $baseLayer = (new BaseLayer(width: 100, height: 100))
            ->add(new TemperatureLayer($perlin))
            ->add(new ElevationLayer($perlin))
            ->add(new IslandLayer())
            ->add(new BiomeLayer())
            ->add(new LandLayer($perlin))
            ->add(new WoodLayer($simplex))
            ->generate();

        return view('mapPreview', [
            'board' => $baseLayer->getBoard(),
        ]);
    }
}
