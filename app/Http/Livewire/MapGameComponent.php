<?php

namespace App\Http\Livewire;

use App\Map\MapGame;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MapGameComponent extends Component
{
    public int $seed;

    public int $offsetX = 0;

    public int $offsetY = 0;

    public float $zoom = 1.0;

    public array $form = [];

    protected $listeners = ['handleKeypress', 'handleClick', 'handleMetaClick'];

    public function render(): View
    {
        $game = MapGame::resolve($this->seed)->persist();

        return view('livewire.mapGameComponent', [
            'board' => $game
                ->baseLayer
                ->generate()
                ->getBoard(),
            'game' => $game,
        ]);
    }

    public function handleClick(int $x, int $y): void
    {
        MapGame::resolve($this->seed)
            ->handleClick($x, $y)
            ->persist();
    }

    public function handleMetaClick(int $x, int $y): void
    {
        MapGame::resolve($this->seed)
            ->showMenu($x, $y)
            ->persist();
    }

    public function resetGame(): void
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
            'Escape' => $this->closeMenu(),
            'ArrowUp', 'w' => $this->handleUp(),
            'ArrowDown', 's' => $this->handleDown(),
            'ArrowLeft', 'a' => $this->handleLeft(),
            'ArrowRight', 'd' => $this->handleRight(),
            '+', '=' => $this->zoomIn(),
            '-', '_' => $this->zoomOut(),
            default => null,
        };
    }

    public function upgradeTile(int $x, int $y): void
    {
        MapGame::resolve($this->seed)
            ->upgradeTile($x, $y)
            ->persist();

        $this->emit('update');
    }

    public function saveMenu(): void
    {
        MapGame::resolve($this->seed)
            ->saveMenu($this->form)
            ->persist();
    }

    public function closeMenu(): void
    {
        MapGame::resolve($this->seed)
            ->closeMenu()
            ->persist();
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
