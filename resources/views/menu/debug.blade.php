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
    <br>
    @if($state = ($tile->state ?? null))
        State: {{ $tile->state->name }}
    @endif
</div>
