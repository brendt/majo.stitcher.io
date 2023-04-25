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
        @if($game->canPay($tile->getUpgradePrice()))
            <div>
                <button wire:click="upgradeTile({{ $tile->x }}, {{ $tile->y }})">
                    {{ $tile->getUpgradeTile()::class }}
                    <br>
                    {{ $tile->getUpgradePrice() }}
                </button>
            </div>
        @else
            <div class="bg-red-500">
                {{ $tile->getUpgradeTile()::class }}
                <br>
                {{ $tile->getUpgradePrice() }}
            </div>
        @endif
    @endif
</div>
