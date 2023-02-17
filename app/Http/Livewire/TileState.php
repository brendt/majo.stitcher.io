<?php

namespace App\Http\Livewire;

enum TileState
{
    case CLOSED;
    case OPEN;
    case OPEN_WITH_MATCH;
    case SELECTED;
    case FOUND;

    public function getStyle(): string
    {
        return match($this) {
            self::CLOSED => 'closed',
            self::OPEN => 'open',
            self::OPEN_WITH_MATCH => 'open-with-match open',
            self::SELECTED => 'selected',
            self::FOUND => 'found',
        };
    }
}
