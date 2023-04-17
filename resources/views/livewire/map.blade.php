<div>
    <style>
        :root {
            --tile-size: {{ 10 * $zoom }}px;
            --tile-border-color: none;
            --tile-gap: 0;
        }

        .board {
            box-shadow: 0 0 10px 0 #00000033;
            border-radius: 2px;
            display: grid;
            max-width: 90%;
            overflow: scroll;
            max-height: 90%;
            grid-template-columns: repeat({{ count($board) }}, var(--tile-size));
            grid-auto-rows: var(--tile-size);
            grid-gap: var(--tile-gap);
            margin: var(--tile-gap);
        }

        .tile {
            width: var(--tile-size);
            height: 100%;
            grid-area: 1 / 1 / 1 / 1;
            background-color: var(--tile-color);
        }

        .tile.tile-border {
            box-shadow: inset 0 0 0 1px var(--tile-border-color);
        }

        .tile:hover {
            box-shadow: inset 0 0 5px 1px #fff;
            cursor: pointer;
        }

        .tile .debug {
            display: none;
            position: absolute;
            background-color: #00000099;
            color: #fff;
            font-weight: bold;
            width: auto;
            height: auto;
            padding: 3px 8px;
            border: 1px solid black;
            border-radius: 2px;
            box-shadow: 0 0 5px 0 #00000066;
            margin-left: 15px;
        }

        .tile:hover .debug {
            display: block;
        }
    </style>

    <div class="">
        <div class="flex flex-col justify-center items-center my-8">
            <div class="board">
                @foreach ($board as $x => $row)
                    @foreach ($row as $y => $tile)
                        <div class="
                            tile
                            {{ $tile instanceof \App\Map\Tile\WithBorder ? 'tile-border' : ''}}
                        " style="
                            grid-area: {{ $y + 1 }} / {{ $x + 1 }} / {{ $y + 1 }} / {{ $x + 1 }};
                            --tile-color:{{ $tile->getColor() }};
                            @if($tile instanceof \App\Map\Tile\WithBorder)--tile-border-color:{{ $tile->getBorderColor() }}@endif
                        ">
                            <div class="debug">
                                Tile: {{ $tile::class }}
                                <br>
                                Biome: {{ ($tile->biome ?? null) ? $tile->biome::class : '?' }}
                                <br>
                                Elevation: {{ $tile->elevation ?? '?' }}
                                <br>
                                Temperature: {{ $tile->temperature ?? '?' }}
                                <br>
                                Color: {{ $tile->getColor() }}
                                <br>
                                Noise: {{ $tile->noise ?? '?' }}
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <div class="bg-black text-white flex justify-between py-2 px-4 mt-4">
                <spa class="mx-4">
                    Wood: 0
                </spa>
                <span class="mx-4">
                    Stone: 0
                </span>
                <span class="mx-4">
                    Gold: 0
                </span>
                <span class="mx-4">
                    Fish: 0
                </span>
                <span class="mx-4">
                    Flax: 0
                </span>
            </div>
        </div>

        <div class="text-sm flex justify-center py-2">
            <span>
                Seed: <a class="underline hover:no-underline" href="/map/{{ $seed }}">{{ $seed }}</a>
            </span>
        </div>
    </div>

    <script>
        window.addEventListener("keydown", function (event) {
            Livewire.emit('handleKeypress', event.key);
        });
    </script>
</div>
