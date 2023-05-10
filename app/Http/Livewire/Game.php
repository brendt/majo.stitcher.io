<?php

namespace App\Http\Livewire;

use App\Game\Board;
use App\Game\ShortBoard;
use Livewire\Component;

class Game extends Component
{
    protected $listeners = ['handleKeypress'];

    public string $type = 'normal';

    public ?string $seed = null;

    private function resolveBoard(): Board|ShortBoard|null
    {
        if ($this->type === 'short') {
            return ShortBoard::resolve();
        }

        return Board::resolve();
    }

    private function initBoard(): Board|ShortBoard
    {
        if ($this->type === 'short') {
            return ShortBoard::init();
        }

        return Board::init($this->seed);
    }

    public function mount(): void
    {
        $board = $this->resolveBoard();

        if ($board === null) {
            $board = $this->initBoard();
        }

        $board->persist();
    }

    public function render()
    {
        $board = $this->resolveBoard();

        return view('livewire.game', [
            'board' => $board,
        ]);
    }

    public function showHint(): void
    {
        $board = $this->resolveBoard();

        if (! $board) {
            return;
        }

        $board->showHint()->persist();
    }

    public function resetBoard(): void
    {
        $board = $this->resolveBoard();

        if (! $board) {
            return;
        }

        $board->destroy();

        $this->initBoard()->persist();
    }

    public function shuffleBoard(): void
    {
        $board = $this->resolveBoard();

        if (! $board) {
            return;
        }

        $board->shuffle()->persist();
    }

    public function handleClick($x, $y): void
    {
        $board = $this->resolveBoard();

        $tileToSelect = $board->get($x, $y);

        if (! $tileToSelect) {
            return;
        }

        if (! $board->canSelect($tileToSelect)) {
            return;
        }

        $highlightedTiles = $board->findHighlightedTiles();

        if ($highlightedTiles) {
            [$a, $b] = $highlightedTiles;

            if ($a->isSame($tileToSelect) || $b->isSame($tileToSelect)) {
                $board->removePair($a, $b);

                return;
            }
        }

        $currentlySelectedTile = $board->getCurrentlySelectedTile();

        if ($currentlySelectedTile?->matches($tileToSelect)) {
            $board->removePair($tileToSelect, $currentlySelectedTile);
        } elseif ($currentlySelectedTile?->isSame($tileToSelect)) {
            $tileToSelect->toggleSelected();
        } else {
            $board->clearSelectedTiles();
            $tileToSelect->toggleSelected();
        }
    }

    public function clickHighlightedTiles(): void
    {
        $board = $this->resolveBoard();

        $highlightedTiles = $board->findHighlightedTiles();

        if ($highlightedTiles) {
            $board->removePair(...$highlightedTiles);
            $board->persist();
        }
    }

    public function handleKeypress(string $key): void
    {
        match ($key) {
            'h' => $this->showHint(),
            's' => $this->shuffleBoard(),
            'Enter' => $this->clickHighlightedTiles(),
            default => null,
        };
    }
}
