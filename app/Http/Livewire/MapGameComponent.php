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

    public function boot(): void
    {
        $game = MapGame::resolve();

        if ($game->menu) {
            $this->form = $game->menu->viewData;
        } else {
            $this->form = [];
        }
    }

    public function saveMenu(): void
    {
        MapGame::resolve($this->seed)
            ->saveMenu($this->form)
            ->persist();

        $this->emit('update');
    }

    public function closeMenu(): void
    {
        MapGame::resolve($this->seed)
            ->closeMenu()
            ->persist();
    }

    public function resetGame(): void
    {
        MapGame::resolve($this->seed)
            ->destroy();
    }

    public function handleKeypress(string $key): void
    {
        match ($key) {
            'Escape' => $this->closeMenu(),
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
}
