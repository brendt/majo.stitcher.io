<?php

namespace App\Map;

use App\Map\Tile\HasMenu;
use Illuminate\View\View;

final readonly class Menu
{
    public function __construct(
        public HasMenu $hasMenu,
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
