<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full bg-zinc-950 font-sans text-zinc-100 antialiased">
        <div class="flex min-h-full flex-col items-center justify-center bg-gradient-to-b from-zinc-950 to-zinc-900 px-4 py-10 sm:px-6">
            <div>
                <a href="/" class="flex flex-col items-center gap-2">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-brand to-brand-muted text-lg font-bold text-white shadow-lg shadow-brand/30">
                        VF
                    </div>
                    <span class="text-sm font-semibold tracking-tight text-white">VendaFacil PDV</span>
                </a>
            </div>

            <div class="mt-8 w-full max-w-md rounded-2xl border border-zinc-800 bg-zinc-900/60 p-6 shadow-2xl shadow-black/40 backdrop-blur sm:p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
