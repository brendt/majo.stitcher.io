<?php

namespace App\Http\Livewire;

use App\Map\MapGame;
use App\Map\Point;
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
            ->handleClick(new Point($x, $y))
            ->persist();

        $this->emit('update');
    }

    public function handleMetaClick(int $x, int $y): void
    {
       MapGame::resolve()
            ->showMenu(new Point($x, $y))
            ->persist();
    }

    public function closeMenu(): void
    {
        MapGame::resolve()
            ->closeMenu()
            ->persist();

        $this->emit('update');
    }

    public function selectItem(string $itemId): void
    {
        MapGame::resolve()
            ->selectItem($itemId)
            ->persist();

        $this->emit('update');
    }

    public function upgradeTile(int $x, int $y, string $upgradeTo): void
    {
        MapGame::resolve()
            ->upgradeTile(
                new Point($x, $y),
                $upgradeTo
            )
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
