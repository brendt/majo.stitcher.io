<div>
    <style>
        :root {
            --tile-size: {{ 10 * $zoom }}px;
            --tile-gap: 0;
        }

        .board {
            box-shadow: 0 0 10px 0 #00000033;
            border-radius: 2px;
            border: 2px solid tomato;
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

        .tile:hover {
            box-shadow: inset 0 0 5px 1px #fff;
            cursor: pointer;
        }

        .tile .debug {
            display: none;
            position: absolute;
            background-color: #fff;
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

    Seed: <a class="underline hover:no-underline" href="/map/{{ $seed }}">{{ $seed }}</a>
    <div class="flex justify-center items-center h-screen pb-16">
        <div class="board">
            @foreach ($board as $x => $row)
                @foreach ($row as $y => $tile)
                    <div class="tile" style="
                        --tile-color:{{ $tile->getColor() }};
                        grid-area: {{ $y + 1 }} / {{ $x + 1 }} / {{ $y + 1 }} / {{ $x + 1 }};
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
    </div>

    <script>
        window.addEventListener("keydown",  function(event) {
            Livewire.emit('handleKeypress', event.key);
        });
    </script>
</div>
