

<div style="min-width:200px"
     x-data="{
        form: {}
     }"
     x-init="form = JSON.parse('{{ json_encode($form) }}');"
>
    <div class="flex justify-between my-2">
        <span class="mx-2">Selling 4 </span>
        <select name="trader-input" x-model="form.input">
            <option value=""></option>
            @foreach(\App\Map\Tile\ResourceTile\Resource::cases() as $resource)
                <option value="{{ $resource->value }}">
                    {{ $resource->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="flex justify-between my-2">
        <span class="mx-2">For 1</span>
        <select name="trader-output" x-model="form.output">
            <option value=""></option>
            @foreach(\App\Map\Tile\ResourceTile\Resource::cases() as $resource)
                <option value="{{ $resource->value }}">
                    {{ $resource->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="my-2 flex justify-end">
        <button class="mx-2" wire:click="closeMenu()">Close</button>
        <button class="mx-2" @click="await saveMenu(form)">Save</button>
    </div>

    <div>
        @if($tile instanceof \App\Map\Tile\Upgradable)
            @if($game->canPay($tile->getUpgradePrice($game)) && $tile->canUpgrade($game))
                <div>
                    <button wire:click="upgradeTile({{ $tile->x }}, {{ $tile->y }})">
                        {{ $tile->getUpgradeTile($game)::class }}
                        <br>
                        {{ $tile->getUpgradePrice($game) }}
                    </button>
                </div>
            @else
                <div class="bg-red-500">
                    {{ $tile->getUpgradeTile($game)::class }}
                    <br>
                    {{ $tile->getUpgradePrice($game) }}
                </div>
            @endif
        @endif
    </div>
</div>

