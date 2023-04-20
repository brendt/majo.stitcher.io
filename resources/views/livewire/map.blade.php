<div wire:poll>
    <style>
        :root {
            --tile-size: {{ 25 }}px;
            --tile-border-color: none;
            --tile-gap: 0;
        }

        .game-window {
            outline: 2px solid red;
            height: 100%;
            width: 100vw;
            overflow: scroll;
            position: relative;
        }

        .board {
            z-index: -1;
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
            box-shadow: inset 0 0 0 2px var(--tile-border-color);
        }

        .tile:hover.clickable {
            box-shadow: inset 0 0 4px 1px #fff;
            cursor: pointer;
        }

        .tile.hasItem {
            box-shadow:
                inset 0 0 0 5px var(--tile-border-color),
                inset 0 0 9px 6px #FFFFFF99
            ;
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
    </style>

    <div class="game-window">
        <div class="menu menu-top flex justify-between py-2 px-2">
                @foreach ($game->handHeldItems as $item)
                    <span class="mx-4">
                    {{ $item->getName() }}
                </span>
                @endforeach
                <span class="mx-4">
                Wood: {{ $game->woodCount }}
            </span>
                <span class="mx-4">
                    Stone: {{ $game->stoneCount }}
                </span>
                <span class="mx-4">
                    Gold: {{ $game->goldCount }}
                </span>
                <span class="mx-4">
                    Fish: {{ $game->fishCount }}
                </span>
                <span class="mx-4">
                    Flax: {{ $game->flaxCount }}
                </span>
            </div>

        <div class="menu menu-left">
            <h1>Shop</h1>

            <ul>
                @foreach($game->getAvailableItems() as $item)
                    <li class="item">
                        @if($game->canBuy($item))
                            @if($item instanceof \App\Map\Item\HandHeldItem)
                                <button wire:click="buyHandHeldItem('{{ $item->getId() }}')">{{ $item->getName() }} {{ $item->getPrice() }}</button>
                            @else
                                <button wire:click="selectItem('{{ $item->getId() }}')">{{ $item->getName() }} {{ $item->getPrice() }}</button>
                            @endif
                        @else
                            <span>
                        {{ $item->getName() }} {{ $item->getPrice() }}
                    </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="menu menu-bottom flex justify-center py-2">
                @if($selectedItem = $game->selectedItem)
                    <span class="mx-2">
                        Selected item: {{ $selectedItem::class }}
                    </span>
                @endif

                <span class="mx-2">
                    Last update: {{ $game->gameTime }}
                </span>

                <span class="mx-2">
                    Seed: <a class="underline hover:no-underline" href="/map/{{ $game->seed }}">{{ $game->seed }}</a>
                </span>

                <span class="mx-2">
                    <button class="underline hover:no-underline" wire:click="resetGame">Reset</button>
                </span>
            </div>

        <div class="board">
            @foreach ($board as $x => $row)
                @foreach ($row as $y => $tile)
                    <div class="
                            tile
                            {{ $tile instanceof \App\Map\Tile\WithBorder ? 'tile-border' : ''}}
                            {{ $tile instanceof \App\Map\Tile\Clickable && $tile->canClick($game) ? 'clickable' : ''}}
                            {{ $tile->item ?? null ? 'hasItem' : '' }}
                        " style="
                            grid-area: {{ $y + 1 }} / {{ $x + 1 }} / {{ $y + 1 }} / {{ $x + 1 }};
                            --tile-color:{{ $tile->getColor() }};
                            @if($tile instanceof \App\Map\Tile\WithBorder)--tile-border-color:{{ $tile->getBorderColor() }}@endif
                        "
                         wire:click.stop="handleClick({{ $x }}, {{ $y }})"

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
            @endforeach
        </div>
    </div>

    <script>
        window.addEventListener("keydown", function (event) {
            Livewire.emit('handleKeypress', event.key);
        });
    </script>
</div>
