<?php

namespace App\Map\Actions;

use App\Map\MapGame;

final readonly class UpdateResourceCount implements Action
{
    public function __construct(
        public int $woodCount = 0,
        public int $goldCount = 0,
        public int $stoneCount = 0,
        public int $flaxCount = 0,
        public int $fishCount = 0,
    ) {}

    public function __invoke(MapGame $game): void
    {
        $game->woodCount += $this->woodCount;
        $game->goldCount += $this->goldCount;
        $game->stoneCount += $this->stoneCount;
        $game->flaxCount += $this->flaxCount;
        $game->fishCount += $this->fishCount;
    }
}
