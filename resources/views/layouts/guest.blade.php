<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="relative flex min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 px-4 py-8">
            <div class="pointer-events-none absolute -top-32 -left-32 h-96 w-96 rounded-full bg-indigo-600/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-32 -right-32 h-96 w-96 rounded-full bg-fuchsia-600/10 blur-3xl"></div>

            <div class="relative w-full sm:max-w-md">
                <div class="mb-8 text-center">
                    <a href="/" class="inline-flex items-center justify-center">
                        <x-application-logo class="h-12 w-12 fill-white drop-shadow-lg" />
                    </a>
                    <h1 class="mt-3 text-2xl font-bold tracking-tight text-white">{{ config('app.name', 'TraceID') }}</h1>
                </div>

                <div class="rounded-2xl bg-white p-6 shadow-2xl shadow-black/40 sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
