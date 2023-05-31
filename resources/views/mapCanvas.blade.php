<html lang="en">
<head>
    <title>Map JS</title>
</head>
<body>

<div style="position: relative">
    <canvas style="z-index:1; position: absolute; left:0; top: 0;" id="overlay" width="{{ $width * $pixelSize }}" height="{{ $height * $pixelSize }}"></canvas>
    <canvas style="z-index:0; position: absolute; left:0; top: 0;" id="map" width="{{ $width * $pixelSize }}" height="{{ $height * $pixelSize }}"></canvas>
</div>

<script>
    const pixelSize = {{ $pixelSize }};
    const map = JSON.parse('{!! json_encode($map)  !!}');
    const canvas = document.getElementById("map");
    const overlay = document.getElementById("overlay");
    const overlayCtx = overlay.getContext("2d");

    overlay.addEventListener('mousemove', function (e) {
        const pixelX = Math.floor(e.offsetX / pixelSize);
        const pixelY = Math.floor(e.offsetY / pixelSize);
        overlayCtx.clearRect(0, 0, overlay.width, overlay.height);
        drawPixel(overlayCtx, pixelX, pixelY, 'red');
    });

    overlay.addEventListener('click', function (e) {
        const pixelX = Math.floor(e.offsetX / pixelSize);
        const pixelY = Math.floor(e.offsetY / pixelSize);
        console.log(pixelX, pixelY);
    })

    function init()
    {
        const ctx = canvas.getContext("2d");

        for (let x = 0; x < map.length; x++) {
            let row = map[x];
            for (let y = 0; y < row.length; y++) {
                drawPixel(ctx, x, y, map[x][y]);
            }
        }
    }

    function drawPixel(ctx, x, y, value)
    {
        ctx.fillStyle = value;
        ctx.lineWidth = 1;
        ctx.fillRect(x * pixelSize, y * pixelSize, pixelSize, pixelSize);
    }

    init();
</script>

</body>
</html>
