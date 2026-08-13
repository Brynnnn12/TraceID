<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TraceID') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <!-- Mobile Container (Putih, di tengah) -->
        <div class="mx-auto flex min-h-screen w-full max-w-md flex-col bg-white shadow-xl sm:border-x sm:border-gray-200">
            <!-- Header Opsional ala Instagram (Bisa dihapus jika tidak perlu) -->
            <div class="sticky top-0 z-10 border-b border-gray-200 bg-white px-4 py-3 text-center">
                <h1 class="text-lg font-semibold tracking-tight">{{ config('app.name', 'TraceID') }}</h1>
            </div>

            <!-- Content Area -->
            <div class="flex-1 pb-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
