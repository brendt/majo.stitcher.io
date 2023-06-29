<?php

namespace App\Http\Controllers;

use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\FishLayer;
use App\Map\Layer\FlaxLayer;
use App\Map\Layer\GoldLayer;
use App\Map\Layer\StoneLayer;
use App\Map\Layer\WoodLayer;
use App\Map\Noise\BasicNoise;
use App\Map\Noise\Perlin2Generator;
use App\Map\Noise\PerlinGenerator;
use App\Map\Noise\ScatterNoise;

final class MapPreviewController
{
    public function __invoke()
    {
        $seed = request()->get('seed', random_int(1, 1_000_000));
        $perlin = new PerlinGenerator($seed);
        $perlin2 = new Perlin2Generator($seed, 100, 100);
        $basicNoise = new BasicNoise($seed);
        $scatterNoise = new ScatterNoise($seed);

//        $baseLayer = (new BaseLayer(100, 100))
//            ->add(new DebugLayer($basicNoise))
//            ->generate();

//        if ($seed % 3 === 0) {
//            $baseLayer->add(new IslandLayer());
//        } else {
//            $baseLayer->add(new ValleyLayer($basicNoise));
//        }


        $baseLayer = (new BaseLayer(100, 100))
//            ->add(new DebugLayer($basicNoise))
            ->add(new BiomeLayer($basicNoise))
            ->add(new WoodLayer($scatterNoise))
            ->add(new FlaxLayer($scatterNoise))
            ->add(new StoneLayer($scatterNoise))
            ->add(new FishLayer($scatterNoise))
            ->add(new GoldLayer($scatterNoise))
            ->generate();

        return view('mapPreview', [
            'board' => $baseLayer->getBoard(),
        ]);
    }
}
