<div {{ $game->paused ? '' : 'wire:poll.1s' }}>
    @if($game->menu ?? null)
        <div class="menu-window">
            <div class="menu tile-menu">
                {{ $game->menu->render($game) }}
            </div>
        </div>
    @endif

    <div class="game-window">
        <div class="menu menu-top flex justify-between py-2 px-2">
            <span class="mx-4">Wood: {{ $game->woodCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Wood) }}/t)</span></span>
            <span class="mx-4">Stone: {{ $game->stoneCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Stone) }}/t)</span></span>
            <span class="mx-4">Gold: {{ $game->goldCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Gold) }}/t)</span></span>
            <span class="mx-4">Fish: {{ $game->fishCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Fish) }}/t)</span></span>
            <span class="mx-4">Flax: {{ $game->flaxCount }} <span class="text-sm">({{ $game->resourcePerTick(\App\Map\Tile\ResourceTile\Resource::Flax) }}/t)</span></span>
        </div>

        <div class="menu menu-left">
            <h1>Inventory</h1>

            <ul>
                @foreach($game->inventory->loopGrouped() as $itemId => $count)
                    <button
                        wire:click="selectItem('{{ $itemId }}')"
                        class="{{ $itemId === $game->selectedItem?->getId() ? 'font-bold' : ''}}"
                    >{{ $itemId }} ({{ $count }})</button>
                @endforeach
            </ul>
        </div>

        <div class="menu menu-bottom flex justify-center py-2">
            @if($selectedItem = $game->selectedItem)
                <span class="mx-2">Selected item: {{ $selectedItem->getName() }}</span>
            @endif
            <span class="mx-2">Last update: {{ $game->gameTime }}</span>
            <span class="mx-2">Seed: <a class="underline hover:no-underline" href="/map/{{ $game->seed }}">{{ $game->seed }}</a></span>
            <span class="mx-2"><a class="underline hover:no-underline" href="{{ action(\App\Http\Controllers\ResetMapController::class) }}">Reset</a></span>
        </div>
    </div>
</div>
