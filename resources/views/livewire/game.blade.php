<div>
    <style>
        :root {
            --tile-size: 60px;
            --tile-gap: 15px;
        }

        .board {
            display: grid;
            grid-template-columns: repeat({{ $board->getXCount() }}, var(--tile-size));
            grid-auto-rows: var(--tile-size);
            grid-gap: var(--tile-gap);
            margin: var(--tile-gap);
        }

        .cell {
            position: relative;
        }

        .tile {
            width: var(--tile-size);
            height: 100%;
            grid-area: 1 / 1 / 1 / 1;
            display: flex;
            align-items: end;
            justify-content: center;
            border: 4px solid var(--tile-color);
            color: var(--tile-color);
            background-color: #fff;
            font-weight: bold;
            padding-bottom: 5px;
            font-size: 1em;
            position: absolute;
            cursor: not-allowed;
        }

        .tile.unselectable {
            filter: blur(1px) grayscale(.7);
        }

        .tile.selectable {
            cursor: pointer;
        }

        .tile.selectable:hover {
            box-shadow: 0 0 0 2px var(--tile-color);
        }

        .tile.highlighted {
            box-shadow: 0 0 5px 5px gold;
        }

        .tile.selected {
            background-color: var(--tile-color);
            color: #fff;
        }
    </style>

    <div class="flex justify-center items-center h-screen pb-16">
        @if($board->isFinished())
            <div>
                FINISHED!

                <button class="underline hover:no-underline" wire:click="resetBoard">New Game</button>
            </div>
        @elseif($board->isStuck())
            <div>
                stuck :(

                <button class="underline hover:no-underline" wire:click="shuffleBoard">Shuffle</button>

                <button class="underline hover:no-underline" wire:click="resetBoard">Reset</button>
            </div>
        @else
            <div>
                <div class="board">
                    @foreach($board->tiles as $x => $row)
                        @foreach($row as $y => $column)
                            <div
                                class="cell"
                                style="grid-area: {{ $y + 1 }} / {{ $x + 1 }} / {{ $y + 1 }} / {{ $x + 1 }}"
                                wire:click="handleClick({{ $x }}, {{ $y }})"
                            >
                                @foreach($column as $z => $tile)
                                    <div
                                        class="
                                        tile
                                        {{ $tile->state->value }}
                                        {{ $board->canSelect($tile) ? 'selectable' : 'unselectable' }}
                                    "
                                        style="
                                        bottom: {{ $z * 3 }}px;
                                        left: {{ $z * -3 }}px;
                                        z-index: {{ $z }};
                                        --tile-color: {{ $tile->getColor() }};
                                    "
                                    >
                                        {{ $tile->value }}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @endforeach
                </div>

                <div class="flex justify-between p-4">
                    <span>Available pairs: {{ $board->getAvailablePairs() }}</span>
                    <span>Tiles left: {{ $board->getTileCount() }}</span>
                    <button class="underline hover:no-underline" wire:click="showHint">Show Hint</button>
                    <button class="underline hover:no-underline" wire:click="resetBoard">Reset</button>
                    <button class="underline hover:no-underline" wire:click="shuffleBoard">Shuffle</button>
                </div>
            </div>
        @endif
    </div>

</div>
