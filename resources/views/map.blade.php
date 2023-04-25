<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Map</title>

    @vite('resources/css/app.css')
    @livewireStyles

    <script>
        function saveMenu(form) {
            fetch(
                '{{ action(\App\Http\Controllers\SaveMenuController::class) }}',
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({form: form}),
                }
            ).then(() => Livewire.emit('closeMenu'));
        }
    </script>

    <script src="https://unpkg.com/alpinejs" defer></script>
    <style>
        :root {
            --tile-size: {{ 25 }}px;
            --tile-border-color: none;
            --tile-border-width: 0px;
            --tile-gap: 0;
        }

        .game-window {
            height: 100%;
            width: 100vw;
            overflow: scroll;
            position: relative;
        }

        .board.overlay {
            z-index: -1;
        }

        .board {
            position: absolute;
            left: 0;
            top: 0;
            z-index: -2;
            box-shadow: 0 0 10px 0 #00000033;
            border-radius: 2px;
            display: grid;
            width: 100%;
            height: 100%;
            overflow: scroll;
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
            box-shadow: inset 0 0 0 var(--tile-border-width) var(--tile-border-color);
        }

        .tile:hover.clickable {
            box-shadow: inset 0 0 4px 1px #fff;
            cursor: pointer;
        }

        .tile.tile-border:hover.clickable {
            box-shadow: inset 0 0 4px 2px var(--tile-border-color);
        }

        .tile.hasItem {
            box-shadow: inset 0 0 0 5px var(--tile-border-color),
            inset 0 0 9px 6px #FFFFFF99;
        }

        .tile .debug {
            -webkit-user-select: none;
            -ms-user-select: none;
            user-select: none;
            display: none;
            position: fixed;
            bottom: 0;
            right: 0;
            font-weight: bold;
            width: auto;
            height: auto;
            margin-left: 35px;
        }

        .tile:hover .debug {
            display: block;
        }

        .menu {
            position: fixed;
            padding: 3px 8px;
            border: 1px solid black;
            border-radius: 2px;
            box-shadow: 0 0 5px 0 #00000066;
            background-color: #00000099;
            color: #fff;
        }

        .menu-top {
            top: 0;
            margin: 0 auto;
            width: 100%;
        }

        .menu-bottom {
            bottom: 0;
            margin: 0 auto;
            width: 100%;
        }

        .tile-menu {
            position: absolute;
            margin-left: 30px;
        }

        .menu-left {
            top: 50px;
            left: 0;
            width: 25%;
        }

        .item > span {
            display: inline-block;
            background-color: red;
        }

        .item > button {
            display: inline-block;
            background-color: green;
        }

        .item + .item {
            margin-top: 5px;
        }

        .menu-window {
            position: fixed;
            width: 100%;
            height: 100%;
            background-color: #33333399;
            z-index: 99;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        select {
            color: #000;
        }

        .metaDown .tile.unclickable {
            box-shadow: inset 0 0 0 20px #00000066;
        }
    </style>
</head>
<body class="">


<script>
    window.addEventListener('keydown', function (event) {
        if (event.key === 'Meta') {
            window.metaDown = true;
            document.querySelector('body').classList.add('metaDown');
        }
    });

    window.addEventListener('keyup', function (event) {
        if (event.key === 'Meta') {
            window.metaDown = false;
            document.querySelector('body').classList.remove('metaDown');
        }
    });
</script>

<livewire:map-game-component :seed="$seed"/>

<div
    x-data="{ tiles: [] }"
    x-init="fetch('/tiles')
        .then(response => response.json())
        .then(response => { tiles = response.tiles })"
    @updatemap="fetch('/tiles')
        .then(response => response.json())
        .then(response => tiles = response.tiles)"
    class="board"
    id="board"
>
    @foreach ($game->loop() as $tile)
        <div class="
                    tile
                    {!! $tile->getStyle($game)->class !!}
                "
             style="
                    grid-area: {{ $tile->y }} / {{ $tile->x }} / {{ $tile->y }} / {{ $tile->x }};
                    {!! $tile->getStyle($game)->style !!}
                "
             x-on:click="
                    window.metaDown
                        ? Livewire.emit('handleMetaClick', {{ $tile->x }}, {{ $tile->y }})
                        : Livewire.emit('handleClick', {{ $tile->x }}, {{ $tile->y }})
                "
        >
            <div class="debug menu">
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

    <template x-for="tile in tiles">
        <div class="tile"
             :style="tile.style.style"
             :class="tile.style.class"
             x-on:click="
                    window.metaDown
                        ? Livewire.emit('handleMetaClick', tile.x, tile.y)
                        : Livewire.emit('handleClick', tile.x, tile.y)
                "
        >
            <div class="debug menu">
                Tile: <span x-text="tile.name"></span>
                <br>
                Biome: <span x-text="tile.biome"></span>
                <br>
                Elevation: <span x-text="tile.elevation"></span>
                <br>
                Temperature: <span x-text="tile.temperature"></span>
            </div>
        </div>
    </template>
</div>

@livewireScripts
<script>
    Livewire.on('update', function () {
        document.getElementById('board').dispatchEvent(new CustomEvent('updatemap'));
    });
</script>
</body>
</html>
