<style>
    :root {
        --pixel-size: 9px;
        --pixel-gap: 1px;
        --pixel-color: #000;
    }

    .map {
        display: grid;
        grid-template-columns: repeat({{ count($pixels) }}, var(--pixel-size));
        grid-auto-rows: var(--pixel-size);
        grid-gap: var(--pixel-gap);
    }

    .map > div {
        width: var(--pixel-size);
        height: 100%;
        grid-area: var(--y) / var(--x) / var(--y) / var(--x);
        background-color: var(--pixel-color);
    }
</style>

<div class="map">
    @foreach($pixels as $x => $row)
        @foreach($row as $y => $pixel)
            {!! $pixel !!}
        @endforeach
    @endforeach
</div>
