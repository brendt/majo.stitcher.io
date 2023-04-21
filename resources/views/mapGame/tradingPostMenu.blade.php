<div style="min-width:200px">
    <div class="flex justify-between my-2">
        <span class="mx-2">Selling 4 </span>
        <select name="trader-input">
            @foreach(\App\Map\Tile\ResourceTile\Resource::cases() as $resource)
                <option value="{{ $resource->name }}">{{ $resource->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-between my-2">
        <span class="mx-2">For 1</span>
        <select name="trader-output">
            @foreach(\App\Map\Tile\ResourceTile\Resource::cases() as $resource)
                <option value="{{ $resource->name }}">{{ $resource->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="my-2">
        <button class="mx-2" wire:click="saveMenu()">Save</button>
    </div>
</div>
