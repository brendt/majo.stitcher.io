<div wire:poll>
    @if($game->menu ?? null)
        <div class="menu-window">
            <div class="menu tile-menu">
                {{ $game->menu->render($game) }}
            </div>
        </div>
    @endif

    <div class="game-window">
        <div class="menu menu-top flex justify-between py-2 px-2">
            @foreach ($game->handHeldItems as $item)
                <span class="mx-4">{{ $item->getName() }}</span>
            @endforeach
            <span class="mx-4">Wood: {{ $game->woodCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Wood) }}/t)</span></span>
            <span class="mx-4">Stone: {{ $game->stoneCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Stone) }}/t)</span></span>
            <span class="mx-4">Gold: {{ $game->goldCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Gold) }}/t)</span></span>
            <span class="mx-4">Fish: {{ $game->fishCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Fish) }}/t)</span></span>
            <span class="mx-4">Flax: {{ $game->flaxCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Flax) }}/t)</span></span>
        </div>

        {{--        <div class="menu menu-left">--}}
        {{--            <h1>Shop</h1>--}}

        {{--            <ul>--}}
        {{--                @foreach($game->getAvailableItems() as $item)--}}
        {{--                    <li class="item">--}}
        {{--                        @if($game->canBuy($item))--}}
        {{--                            @if($item instanceof \App\Map\Item\HandHeldItem)--}}
        {{--                                <button wire:click="buyHandHeldItem('{{ $item->getId() }}')">{{ $item->getName() }} {{ $item->getPrice() }}</button>--}}
        {{--                            @else--}}
        {{--                                <button wire:click="selectItem('{{ $item->getId() }}')">{{ $item->getName() }} {{ $item->getPrice() }}</button>--}}
        {{--                            @endif--}}
        {{--                        @else--}}
        {{--                            <span>--}}
        {{--                        {{ $item->getName() }} {{ $item->getPrice() }}--}}
        {{--                    </span>--}}
        {{--                        @endif--}}
        {{--                    </li>--}}
        {{--                @endforeach--}}
        {{--            </ul>--}}
        {{--        </div>--}}

        <div class="menu menu-bottom flex justify-center py-2">
            @if($selectedItem = $game->selectedItem)
                <span class="mx-2">Selected item: {{ $selectedItem::class }}</span>
            @endif
            <span class="mx-2">Last update: {{ $game->gameTime }}</span>
            <span class="mx-2">Seed: <a class="underline hover:no-underline" href="/map/{{ $game->seed }}">{{ $game->seed }}</a></span>
            <span class="mx-2"><button class="underline hover:no-underline" wire:click="resetGame">Reset</button></span>
        </div>
    </div>
</div>
