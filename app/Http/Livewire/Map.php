<?php

namespace App\Http\Livewire;

use App\Map\MapGame;
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
        $game = MapGame::resolve($this->seed)->persist();

        return view('livewire.map', [
            'board' => $game
                ->baseLayer
                ->generate()
                ->getBoard(),
            'game' => $game,
        ]);
    }

    public function handleClick($x, $y): void
    {
        MapGame::resolve($this->seed)
            ->handleClick($x, $y)
            ->persist();
    }

    public function resetGame()
    {
        MapGame::resolve($this->seed)
            ->destroy();
    }

    public function buyHandHeldItem(string $itemId): void
    {
        MapGame::resolve($this->seed)
            ->buyHandHeldItem($itemId)
            ->persist();
    }

    public function selectItem(string $itemId): void
    {
        MapGame::resolve($this->seed)
            ->selectItem($itemId)
            ->persist();
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
