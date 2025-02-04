<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>TestView</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
<div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold">Test View</h1>
        <p>
            @php(dump($result ?? 'no result'))
        </p>
    </div>
    @if ($imageUrl)
        <div>
            <img src="{{ $imageUrl }}"/>
        </div>
    @endif
</div>
</body>
