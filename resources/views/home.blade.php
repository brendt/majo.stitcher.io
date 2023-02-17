<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mahjong</title>

    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-blue-100">

<livewire:game :seed="$seed"/>

@livewireScripts
</body>
</html>
