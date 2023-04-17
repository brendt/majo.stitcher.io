<?php

namespace App\Http\Livewire;

use App\Map\Layer\BaseLayer;
use App\Map\Layer\BiomeLayer;
use App\Map\Layer\ElevationLayer;
use App\Map\Layer\FishLayer;
use App\Map\Layer\TemperatureLayer;
use App\Map\Layer\LandLayer;
use App\Map\Layer\VegetationLayer;
use App\Map\Noise\PerlinGenerator;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Map extends Component
{
    public int $seed;

    public int $offsetX = 0;

    public int $offsetY = 0;

    public float $zoom = 1.0;

    protected $listeners = ['handleKeypress'];

    public function render(): View
    {
        $generator = new PerlinGenerator($this->seed);

        $board = (new BaseLayer(width: 100, height: 70))
            ->add(new TemperatureLayer($generator))
            ->add(new ElevationLayer($generator))
            ->add(new BiomeLayer())
            ->add(new LandLayer($generator))
            ->add(new VegetationLayer($generator))
            ->add(new FishLayer($generator))
            ->generate();

        return view('livewire.map', [
            'board' => $board,
        ]);
    }

    public function handleKeypress(string $key): void
    {
        match ($key) {
            'ArrowUp', 'w' => $this->handleUp(),
            'ArrowDown', 's' => $this->handleDown(),
            'ArrowLeft', 'a' => $this->handleLeft(),
            'ArrowRight', 'd' => $this->handleRight(),
            '+', '=' => $this->zoomIn(),
            '-', '_' => $this->zoomOut(),
            default => null,
        };
    }

    public function handleUp(): void
    {
        if ($this->offsetY <= -100) {
            return;
        }

        $this->offsetY -= 10;
    }

    public function handleDown(): void
    {
        if ($this->offsetY >= 100) {
            return;
        }

        $this->offsetY += 10;
    }

    public function handleLeft(): void
    {
        if ($this->offsetX <= -100) {
            return;
        }

        $this->offsetX -= 10;
    }

    public function handleRight(): void
    {
        if ($this->offsetX >= 100) {
            return;
        }

        $this->offsetX += 10;
    }

    private function zoomIn(): void
    {
        if ($this->zoom >= 3.0) {
            return;
        }

        $this->zoom += 0.5;
    }

    private function zoomOut(): void
    {
        if ($this->zoom <= 1.0) {
            return;
        }

        $this->zoom -= 0.5;
    }
}
