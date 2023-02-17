<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Game extends Component
{
    public ?int $seed = null;

    public function mount()
    {
        StandardBoard::init($this->seed);
    }

    public function handleClick($x, $y)
    {
        $board = StandardBoard::resolve();

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
        } elseif ($pendingSelected->valueNotEquals($previouslySelected)) {
            $previouslySelected->markUnselected();
            $pendingSelected->markSelected();
        } else {
            $pendingSelected->toggleSelected();
        }

        $board->persist();
    }

    public function shuffleBoard()
    {
        $board = StandardBoard::resolve();

        if ($board->getAvailableShuffles() === 0) {
            return;
        }

        $board
            ->shuffle()
            ->persist();
    }

    public function resetBoard()
    {
        $board = StandardBoard::resolve();

        $board
            ->reset($board->seed)
            ->persist();
    }

    public function newBoard()
    {
        StandardBoard::resolve()
            ->reset()
            ->persist();
    }

    public function showHint()
    {
        StandardBoard::resolve()
            ->showHint()
            ->persist();
    }

    public function render()
    {
        return view('livewire.game', [
            'board' => StandardBoard::resolve(),
        ]);
    }
}
