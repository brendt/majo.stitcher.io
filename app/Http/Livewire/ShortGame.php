<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ShortGame extends Component
{
    public function mount()
    {
        $board = ShortBoard::init();

        dump($board);
    }

    public function handleClick($x, $y)
    {
        $board = ShortBoard::resolve();

        $pendingSelected = $board->get($x, $y);

        if (! $pendingSelected) {
            return;
        }

        if (! $board->canSelect($pendingSelected)) {
            return;
        }

        if ($pendingSelected->isFound()) {
            return;
        }

        $previouslySelected = $board->findSelected();

        if ($pendingSelected->valueEquals($previouslySelected)) {
            $previouslySelected->markAsFound();
            $pendingSelected->markAsFound();
            $board->handleEmptyRows();
        } elseif ($pendingSelected->valueNotEquals($previouslySelected)) {
            $previouslySelected->markUnselected();
            $pendingSelected->markSelected();
        } else {
            $pendingSelected->toggleSelected();
        }

        $board->persist();
    }

    public function newBoard()
    {
        ShortBoard::resolve()
            ->reset()
            ->persist();
    }

    public function testBoard()
    {
        ShortBoard::resolve()
            ->handleEmptyRows()
            ->persist();
    }

    public function shuffleBoard()
    {
        ShortBoard::resolve()
            ->shuffle()
            ->persist();
    }

    public function render()
    {
        return view('livewire.shortGame', [
            'board' => ShortBoard::resolve(),
        ]);
    }
}
