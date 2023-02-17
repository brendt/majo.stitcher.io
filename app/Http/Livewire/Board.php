<?php

namespace App\Http\Livewire;

interface Board
{
    public function canSelect(Tile $tile): bool;

    public function remove(Tile $tile): static;

    public function get(int $x, int $y, ?int $z = null): ?Tile;

    public function hasOpenMatch(Tile $tile): bool;
}
