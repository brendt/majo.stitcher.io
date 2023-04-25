<?php

namespace App\Http\Livewire;

use App\Map\MapGame;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MapGameComponent extends Component
{
    public int $seed;

    protected $listeners = ['handleKeypress', 'handleClick', 'handleMetaClick', 'closeMenu'];

    public function render(): View
    {
        $game = MapGame::resolve()->persist();

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
        MapGame::resolve()
            ->handleClick($x, $y)
            ->persist();
    }

    public function handleMetaClick(int $x, int $y): void
    {
       MapGame::resolve()
            ->showMenu($x, $y)
            ->persist();
    }

    public function closeMenu(): void
    {
        MapGame::resolve()
            ->closeMenu()
            ->persist();

        $this->emit('update');
    }

    public function resetGame(): void
    {
        MapGame::resolve()
            ->destroy();
    }

    public function upgradeTile(int $x, int $y): void
    {
        MapGame::resolve()
            ->upgradeTile($x, $y)
            ->persist();

        $this->emit('update');
    }

    public function handleKeypress(string $key): void
    {
        match ($key) {
            'Escape' => $this->closeMenu(),
            default => null,
        };
    }
}
