<?php

namespace App\Map;

use Illuminate\View\View;

final readonly class Menu
{
    public function __construct(
        public string $viewPath,
        public array $viewData,
    ) {}

    public function render(MapGame $game): View
    {
        return view(
            $this->viewPath,
            ['game' => $game, ...$this->viewData],
        );
    }
}
