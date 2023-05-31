<html lang="en">
<head>
    <title>Map JS</title>
</head>
<body>

<canvas id="map" width="1000" height="750"></canvas>

<script>
    const pixelSize = 3;
    const latticeSize = 30;
    const seed = 123;

    function init()
    {
        const canvas = document.getElementById("map");
        const ctx = canvas.getContext("2d");
        const width = canvas.width / pixelSize;
        const height = canvas.height / pixelSize;

        for(let x = 1; x <= width; x++) {
            for(let y = 1; y <= height; y++) {
                drawPixel(ctx, x, y, noise(width, height, x, y));
            }
        }
    }

    function toHex(value)
    {
        return value.toString(16).slice(-2);
    }

    function drawPixel(ctx, x, y, value)
    {
        const hexFromNoise = toHex(value);

        let color = `#${hexFromNoise}${hexFromNoise}${hexFromNoise}`;

        // if (value < 0.6) {
        //     color = `#0000${hexFromNoise}`;
        // } else {
        //     color = `#00${hexFromNoise}00`;
        // }

        ctx.fillStyle = color;
        ctx.lineWidth = 1;
        ctx.fillRect(x * pixelSize, y * pixelSize, pixelSize, pixelSize);
    }

    function lerp(a, b, fraction)
    {
        return Math.floor((1 - fraction) * a + fraction * b);
    }

    function noise(width, height, x, y)
    {
        return Math.floor(((baseNoise(x, y) / 256) * (circularNoise(width, height, x, y) / 256)) * 256);
    }

    function circularNoise(width, height, x, y)
    {
        const middleX = width / 2;
        const middleY = height / 2;

        const distanceFromMiddle = Math.floor(Math.sqrt(
            Math.pow((x - middleX), 2)
            + Math.pow((y - middleY), 2)
        ));

        const maxDistanceFromMiddle = Math.floor(Math.sqrt(
            Math.pow((width - middleX), 2)
            + Math.pow((height - middleY), 2)
        ));

        return 1 - Math.floor((distanceFromMiddle / maxDistanceFromMiddle) * 256);
    }

    function baseNoise(x, y)
    {
        if (x % latticeSize === 0 && y % latticeSize === 0) {
            return hash(x, y);
        }
        else if(x % latticeSize === 0)
        {
            const top = Math.floor(y / latticeSize) * latticeSize;
            const bottom = Math.ceil(y / latticeSize) * latticeSize;

            return lerp(
                hash(x, top),
                hash(x, bottom),
                (y - top) / (bottom - top)
            );
        }
        else if (y % latticeSize === 0)
        {
            const left = Math.floor(x / latticeSize) * latticeSize;
            const right = Math.ceil(x / latticeSize) * latticeSize;

            return lerp(
                hash(left, y),
                hash(right, y),
                (x - left) / (right - left)
            );
        }
        else {
            const top = Math.floor(y / latticeSize) * latticeSize;
            const bottom = Math.ceil(y / latticeSize) * latticeSize;
            const left = Math.floor(x / latticeSize) * latticeSize;
            const right = Math.ceil(x / latticeSize) * latticeSize;

            const a = lerp(
                hash(left, top),
                hash(right, top),
                (x - left) / (right - left)
            );

            const b = lerp(
                hash(left, bottom),
                hash(right, bottom),
                (x - left) / (right - left)
            );

            return lerp(
                a,
                b,
                (y - top) / (bottom - top)
            );
        }
    }

    function hash(x, y)
    {
        const value = seed * x * y;

        const hash = cyrb53('hash' + value, seed);

        return hash % 256;
    }

    // https://stackoverflow.com/questions/7616461/generate-a-hash-from-string-in-javascript
    TSH=s=>{for(var i=0,h=9;i<s.length;)h=Math.imul(h^s.charCodeAt(i++),9**9);return h^h>>>9}
    const cyrb53 = (str, seed = 0) => {
        let h1 = 0xdeadbeef ^ seed, h2 = 0x41c6ce57 ^ seed;
        for(let i = 0, ch; i < str.length; i++) {
            ch = str.charCodeAt(i);
            h1 = Math.imul(h1 ^ ch, 2654435761);
            h2 = Math.imul(h2 ^ ch, 1597334677);
        }
        h1  = Math.imul(h1 ^ (h1 >>> 16), 2246822507);
        h1 ^= Math.imul(h2 ^ (h2 >>> 13), 3266489909);
        h2  = Math.imul(h2 ^ (h2 >>> 16), 2246822507);
        h2 ^= Math.imul(h1 ^ (h1 >>> 13), 3266489909);

        return 4294967296 * (2097151 & h2) + (h1 >>> 0);
    };


    init();
</script>

</body>
</html>
