<div class="text-center">
    <style>
        .board {
            position: relative;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(16, 50px);
            grid-auto-rows: 60px;
            grid-gap: 9px;
            margin: 20px;
        }

        .cell {
            position: relative;
        }

        .tile {
            width: 50px;
            height: 100%;
            grid-area: 1 / 1 / 1 / 1;
            position: absolute;
            display: flex;
            align-items: end;
            justify-content: center;
            /*border-radius: 4px;*/
            background-color: var(--color);
            border: 0;
            box-shadow: inset 0px 0px 0 1px rgba(0, 0, 0, 0.5),
            inset -2px -2px 0 4px rgba(0, 0, 0, 0.4);
            padding-bottom: 5px;
            border-radius: 4% 10%;
            cursor: not-allowed;
        }

        .tile.selected {
            font-weight: bold;
            border: 2px solid black;
            cursor: pointer;
        }

        .tile.open {
            cursor: pointer;
        }

        .tile.open:hover {
            box-shadow: inset 0px 0px 0 1px rgba(0, 0, 0, 0.5),
            inset -2px -2px 0 4px rgba(0, 0, 0, 0.4),
            0 0 0px 1px black;
        }

        .tile.open-with-match {
            /*border: 2px solid yellow;*/
            cursor: pointer;
        }

        .tile.closed {
            box-shadow: inset 0 0 0 100px rgba(0, 0, 0, 0.3),
            inset 0px 0px 0 1px rgba(0, 0, 0, 0.5),
            inset -2px -2px 0 4px rgba(0, 0, 0, 0.4);
        }

        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
    </style>

    <div class="text-center">
        <p>Play until the board is empty!</p>
    </div>

    <div class="flex justify-center items-center h-screen pb-16">
        <div class="">
            <div class="board">
                @foreach($board->tiles as $y => $row)
                    @foreach($row as $x => $tiles)
                        @foreach($tiles as $z => $tile)
                            <div
                                style="
                                    grid-area: {{ 5 - $y + 1 }} / {{ $x + 1 }} / {{ 5 - $y + 1 }} / {{ $x + 1 }}
                                "
                                wire:click="handleClick({{ $x }}, {{ $y }})"
                                class="cell"
                            >
                                @if ($tile)
                                    <div
                                        class="
                                    {{ $tile->getState()->getStyle() }}
                                    tile
                                "
                                        style="
                                            bottom: {{ $z * 3 }}px;
                                            left: {{ $z * -3 }}px;
                                            z-index: {{ $z }};
                                            --color: {{ $tile->getColor() }};
                                        "
                                    >
                                        {{ $tile->value }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @endforeach
                @endforeach
            </div>
            <div class="flex justify-between">
                <span>
                    Available Pairs: {{ $board->getAvailablePairs() }}
                </span>
                <span>
                    Score: {{ $board->score }}
                </span>
                <button class="underline hover:no-underline" wire:click="newBoard">New</button>
            </div>
        </div>
    </div>

    <script src="/confetti.min.js"></script>

    @if($board->isDone())
        <canvas class="confetti" id="confetti"></canvas>
        <script>
            var confettiSettings = {
                target: 'confetti',
                max: 200,
                clock: 50,
                rotate: true,
            };
            var confetti = new ConfettiGenerator(confettiSettings);
            confetti.render();
        </script>
    @endif
</div>
