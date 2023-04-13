<?php

namespace App\Game;

enum TileState: string
{
    case DEFAULT = 'default';
    case SELECTED = 'selected';
    case HIGHLIGHTED = 'highlighted';
}
