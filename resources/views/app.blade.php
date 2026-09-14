<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ getSiteSetting()?->company_name ?: config('app.name', 'Car Rental System') }}</title>
    <link id="app-favicon" rel="icon" type="image/x-icon" href="{{ getFavIcon() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @routes
    @vite(['resources/ts/app.ts'])
    @inertiaHead
</head>
<body class="font-sans antialiased bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100">
    @inertia
</body>
</html>
