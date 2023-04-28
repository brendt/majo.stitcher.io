<?php

namespace App\Http\Controllers;

use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\FishLayer;
use App\Map\Layer\FlaxLayer;
use App\Map\Layer\GoldLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\StoneLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\PerlinGenerator;

final class MapPreviewController
{
    public function __invoke()
    {
        $generator = new PerlinGenerator(random_int(1, 1_000_000));

        $baseLayer = (new BaseLayer(width: 500, height: 400))
            ->add(new TemperatureLayer($generator))
            ->add(new ElevationLayer($generator))
            ->add(new BiomeLayer())
            ->add(new LandLayer($generator))
            ->add(new WoodLayer($generator))
            ->add(new StoneLayer($generator))
            ->add(new FishLayer($generator))
            ->add(new GoldLayer($generator))
            ->add(new FlaxLayer($generator))
            ->generate();

        return view('mapPreview', [
            'board' => $baseLayer->getBoard(),
        ]);
    }
}
