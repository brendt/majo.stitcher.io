@php
    /** @var \App\Map\MapGame $game */
@endphp

<div>
    <button
        wire:click="closeMenu"
    >
        Close
    </button>

    @if($tile instanceof \App\Map\Tile\Upgradable)
        @foreach($tile->canUpgradeTo($game) as $canUpgradeTo)
            <div>
                @if($game->canPay($canUpgradeTo->getPrice($game)))
                    <div>
                        <button wire:click="upgradeTile({{ $tile->x }}, {{ $tile->y }}, '{{ $canUpgradeTo->getName() }}')">
                            {{ $canUpgradeTo::class }}
                            <br>
                            {{ $canUpgradeTo->getPrice($game) }}
                        </button>
                    </div>
                @else
                    <div class="bg-red-500">
                        {{ $canUpgradeTo::class }}
                        <br>
                        {{ $canUpgradeTo->getPrice($game) }}
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
