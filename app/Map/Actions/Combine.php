<?php

namespace App\Map\Actions;

use App\Map\MapGame;

final readonly class Combine implements Action
{
    /** @var \Illuminate\Notifications\Action[] */
    private array $actions;

    public function __construct(Action ...$actions)
    {
        $this->actions = $actions;
    }

    public function __invoke(MapGame $game): void
    {
        foreach ($this->actions as $action) {
            $action($game);
        }
    }
}
