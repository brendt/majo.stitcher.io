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
use App\Map\Noise\ScatterNoise;

final class MapCanvasController
{
    public function __invoke()
    {
        $seed = request()->get('seed', random_int(1, 1_000_000));

        $width = 200;
        $height = 120;
        $pixelSize = 7;

        $basicNoise = new BasicNoise($seed);
        $scatterNoise = new ScatterNoise($seed);

//        $baseLayer = (new BaseLayer($width, $height))
//            ->add(new DebugLayer($basicNoise))
//            ->generate();


        $baseLayer = (new BaseLayer($width, $height))
            ->add(new BiomeLayer($basicNoise))
            ->add(new WoodLayer($scatterNoise))
            ->add(new FlaxLayer($scatterNoise))
            ->add(new StoneLayer($scatterNoise))
            ->add(new FishLayer($scatterNoise))
            ->add(new GoldLayer($scatterNoise))
            ->generate();


        $json = [];

        foreach ($baseLayer->getBoard() as $x => $row) {
            foreach ($row as $y => $tile) {
                $json[$x][$y] = $tile->getBiome()->getTileColor($tile);
            }
        }

        return view('mapCanvas', [
            'map' => $json,
            'width' => $width,
            'height' => $height,
            'pixelSize' => $pixelSize,
        ]);
    }
}
