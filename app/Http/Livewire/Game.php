<?php

namespace App\Http\Livewire;

use App\Game\Board;
use Livewire\Component;

class Game extends Component
{
    public function mount(): void
    {
//        $board = Board::init();

        $board = Board::resolve();

        if ($board === null) {
            $board = Board::init();
        }

        $board->persist();
    }

    public function render()
    {
        $board = Board::resolve();

        return view('livewire.game', [
            'board' => $board,
        ]);
    }

    public function showHint(): void
    {
        $board = Board::resolve();

        if (! $board) {
            return;
        }

        $board->showHint()->persist();
    }

    public function resetBoard(): void
    {
        $board = Board::resolve();

        if (! $board) {
            return;
        }

        $board->destroy();

        Board::init()->persist();
    }

    public function shuffleBoard(): void
    {
        $board = Board::resolve();

        if (! $board) {
            return;
        }

        $board->shuffle()->persist();
    }

    public function handleClick($x, $y): void
    {
        $board = Board::resolve();

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
                $board->remove($a);
                $board->remove($b);

                return;
            }
        }

        $currentlySelectedTile = $board->getCurrentlySelectedTile();

        if ($currentlySelectedTile?->matches($tileToSelect)) {
            $board->remove($currentlySelectedTile);
            $board->remove($tileToSelect);
        } elseif ($currentlySelectedTile?->isSame($tileToSelect)) {
            $tileToSelect->toggleSelected();
        } else {
            $board->clearSelectedTiles();
            $tileToSelect->toggleSelected();
        }
    }
}
