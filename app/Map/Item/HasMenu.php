<?php

namespace App\Map\Item;

use Illuminate\View\View;

interface HasMenu
{
    public function menuShown(): bool;

    public function toggleMenu(): void;

    public function saveMenu(): void;

    public function getMenu(): View;
}
