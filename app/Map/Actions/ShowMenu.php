<?php

namespace App\Map\Actions;

use App\Map\MapGame;
use App\Map\Menu;

final readonly class ShowMenu implements Action
{
    public function __construct(
        private string $viewPath,
        private array $viewData,
    ) {}

    public function __invoke(MapGame $game)
    {
        $game->menu = new Menu($this->viewPath, $this->viewData);
    }
}
