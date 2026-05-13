<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VendaFacil PDV') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full bg-zinc-950 font-sans antialiased text-zinc-100">
        <div x-data="{ sidebarOpen: false }" class="min-h-full">
            {{-- Mobile overlay --}}
            <div
                x-show="sidebarOpen"
                x-transition.opacity
                class="fixed inset-0 z-30 bg-black/70 backdrop-blur-sm lg:hidden"
                style="display: none;"
                @click="sidebarOpen = false"
            ></div>

            @include('layouts.navigation')

            <div class="lg:pl-64">
                @isset($header)
                    <header class="sticky top-0 z-20 border-b border-zinc-800/90 bg-zinc-950/90 px-4 py-4 backdrop-blur sm:px-6 lg:px-8">
                        {{ $header }}
                    </header>
                @endisset

                <main class="min-h-[calc(100vh-4rem)] p-4 sm:p-6 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
