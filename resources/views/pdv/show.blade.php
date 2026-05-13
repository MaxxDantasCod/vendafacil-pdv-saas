<x-app-layout>
    <x-slot name="header">
        <h1 class="text-xl font-semibold tracking-tight text-white">{{ __('PDV — TakePOS') }}</h1>
    </x-slot>

    <div class="mx-auto max-w-[1600px]">
        <div class="overflow-hidden rounded-2xl border border-zinc-800 bg-zinc-900/40 shadow-2xl shadow-black/40 ring-1 ring-white/5">
            <iframe
                title="TakePOS Dolibarr"
                src="{{ $iframeSrc }}"
                class="h-[calc(100vh-11rem)] min-h-[480px] w-full border-0 bg-black"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>
        <p class="mt-3 text-center text-xs text-zinc-500">
            <span class="font-mono text-zinc-400">{{ $iframeSrc }}</span>
        </p>
    </div>
</x-app-layout>
