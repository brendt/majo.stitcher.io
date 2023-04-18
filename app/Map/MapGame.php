<?php

namespace App\Map;

use App\Map\Item\Item;
use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\FishLayer;
use App\Map\Layer\FlaxLayer;
use App\Map\Layer\GoldVeinLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\StoneVeinLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\TreeLayer;
use App\Map\Noise\PerlinGenerator;
use App\Map\Tile\Clickable;
use App\Map\Tile\HandlesTicks;
use Illuminate\Support\Facades\Session;

final class MapGame
{
    public function __construct(
        public int $seed,
        public BaseLayer $baseLayer,
        public int $woodCount = 0,
        public int $stoneCount = 0,
        public int $goldCount = 0,
        public int $fishCount = 0,
        public int $flaxCount = 0,
        public ?Item $selectedItem = null,
        public int $gameTime = 0,
    ) {}

    public static function resolve(?int $seed = null): self
    {
        if ($fromSession = Session::get('map')) {
            return unserialize($fromSession);
        }

        return self::init($seed ?? 1);
    }

    public function persist(): self
    {
        $this->updateGameTime();

        Session::put('map', serialize($this));

        return $this;
    }

    public function destroy(): void
    {
        Session::remove('map');
    }

    public static function init(int $seed): self
    {
        $generator = new PerlinGenerator($seed);

        $baseLayer = (new BaseLayer(width: 100, height: 70))
            ->add(new TemperatureLayer($generator))
            ->add(new ElevationLayer($generator))
            ->add(new BiomeLayer())
            ->add(new LandLayer($generator))
            ->add(new TreeLayer($generator))
            ->add(new FishLayer($generator))
            ->add(new GoldVeinLayer($generator))
            ->add(new StoneVeinLayer($generator))
            ->add(new FlaxLayer($generator))
            ->generate();

        return new self(
            seed: $seed,
            baseLayer: $baseLayer,
            gameTime: time(),
        );
    }

    public function handleClick($x, $y): self
    {
        $tile = $this->baseLayer->get($x, $y);

        if ($tile instanceof Clickable && $tile->canClick($this)) {
            $tile->handleClick($this);
        }

        return $this;
    }

    public function selectItem(string $className): self
    {
        $itemClass = '\\App\\Map\\Item\\' . $className;

        $this->selectedItem = new $itemClass;

        return $this;
    }

    public function unselectItem(): void
    {
        $this->selectedItem = null;
    }

    private function updateGameTime(): void
    {
        $oldTime = $this->gameTime;

        $newTime = time();

        $difference = $newTime - $oldTime;

        foreach ($this->baseLayer->loop() as $tile) {
            if ($tile instanceof HandlesTicks) {
                $tile->handleTicks($this, $difference);
            }
        }

        $this->gameTime = $newTime;
    }
}
