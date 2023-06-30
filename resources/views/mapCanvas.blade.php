<html lang="en">
<head>
    <title>Map JS</title>

    <style>
        html, body {
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            background-color: #B05C52;
            padding: 10px;
        }

        .board {
            position: relative;
            box-shadow: 0 20px 50px -3px black;
            border: 6px solid #0270A5;
            border-radius: 1px;
            overflow: auto;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="board" style="width:{{ $width * $pixelSize }}px; height: {{ $height * $pixelSize }}px;">
        <canvas style="z-index:1; position: absolute; left:0; top: 0;" id="overlay" width="{{ $width * $pixelSize }}"
                height="{{ $height * $pixelSize }}"></canvas>
        <canvas style="z-index:0; position: absolute; left:0; top: 0;" id="map" width="{{ $width * $pixelSize }}"
                height="{{ $height * $pixelSize }}"></canvas>
    </div>
</div>

<script>
    const pixelSize = {{ $pixelSize }};
    let scale = 20;
    const map = JSON.parse('{!! json_encode($map)  !!}');
    const canvas = document.getElementById("map");
    const board = document.getElementsByClassName("board")[0];
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
    });

    board.addEventListener('wheel', function (e) {
        if (!e.shiftKey) {
            return;
        }

        e.preventDefault();

        const zoomIn = e.deltaX < 0 || e.deltaY < 0;

        if (zoomIn) {
            if (scale >= 50) {
                return;
            }

            scale += 2;
        } else {
            if (scale <= 20) {
                return;
            }

            scale -= 2;
        }

        draw();
    });

    function draw() {
        const ctx = canvas.getContext("2d");

        for (let x = 0; x < map.length; x++) {
            let row = map[x];
            for (let y = 0; y < row.length; y++) {
                drawPixel(ctx, x, y, map[x][y]);
            }
        }
    }

    function drawPixel(ctx, x, y, value) {
        const scaledPixelSize = (scale / 20) * pixelSize;

        ctx.fillStyle = value;
        ctx.lineWidth = 1;
        ctx.fillRect(x * scaledPixelSize, y * scaledPixelSize, scaledPixelSize, scaledPixelSize);
    }

    draw();
</script>

</body>
</html>
