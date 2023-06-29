<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Map</title>

    @vite('resources/css/app.css')

    <style>
        :root {
            --tile-size: 8px;
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
            margin: 0 auto;
            z-index: -2;
            border-radius: 2px;
            display: grid;
            width: 100%;
            height: 100%;
            overflow: scroll;
            grid-template-columns: repeat({{ count($board) }}, var(--tile-size));
            grid-auto-rows: var(--tile-size);
            grid-gap: var(--tile-gap);
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

        .clickable-LandTile .tile-LandTile:hover,
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
            bottom: 40px;
            right: 10px;
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

<div
    class="board"
    id="board"
>
    @foreach ($board as $row)
        @foreach($row as $tile)
            <div class="tile"
                 style="
                        grid-area: {{ $tile->getPoint()->y }} / {{ $tile->getPoint()->x }} / {{ $tile->getPoint()->y }} / {{ $tile->getPoint()->x }};
                        --tile-color: {{ $tile->getColor() }};
                    "
            >
                @if($tile instanceof \App\Map\Tile\HasTooltip)
                    {!! $tile->getTooltip() !!}
                @endif
            </div>
        @endforeach
    @endforeach
</div>
</body>
</html>
