@php
    $nav = [
        ['route' => 'dashboard', 'label' => __('Dashboard'), 'icon' => 'home'],
//      ['route' => 'pdv.show', 'label' => __('PDV'), 'icon' => 'pos'],
        ['route' => 'pdv.index', 'label' => __('PDV'), 'icon' => 'pos'],
        ['route' => 'produtos.index', 'label' => __('Produtos'), 'icon' => 'box'],
        ['route' => 'clientes.index', 'label' => __('Clientes'), 'icon' => 'users'],
        ['route' => 'reports.index', 'label' => __('Relatórios'), 'icon' => 'chart'],
    ];
@endphp

{{-- Mobile top bar --}}
<div class="sticky top-0 z-20 flex h-14 items-center justify-between gap-3 border-b border-zinc-800 bg-zinc-900/95 px-4 backdrop-blur lg:hidden">
    <button
        type="button"
        class="inline-flex items-center justify-center rounded-lg border border-zinc-700 p-2 text-zinc-300 hover:bg-zinc-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-brand"
        @click="sidebarOpen = true"
        aria-label="{{ __('Abrir menu') }}"
    >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <a href="{{ route('dashboard') }}" class="flex flex-col leading-tight">
        <span class="text-sm font-semibold tracking-tight text-white">VendaFacil</span>
        <span class="text-[10px] font-medium uppercase tracking-widest text-brand-muted">PDV</span>
    </a>
    <div class="w-10"></div>
</div>

{{-- Sidebar --}}
<aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-zinc-800 bg-zinc-900 transition-transform duration-200 ease-out lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-zinc-800 px-5">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-brand to-brand-muted text-xs font-bold text-white shadow-lg shadow-brand/30">
            VF
        </div>
        <a href="{{ route('dashboard') }}" class="min-w-0 flex-1 leading-tight" @click="sidebarOpen = false">
            <span class="block truncate text-base font-semibold tracking-tight text-white">VendaFacil PDV</span>
            <span class="block truncate text-[11px] text-zinc-500">{{ __('Ponto de venda') }}</span>
        </a>
        <button
            type="button"
            class="shrink-0 rounded-lg p-1.5 text-zinc-500 hover:bg-zinc-800 hover:text-white lg:hidden"
            @click="sidebarOpen = false"
            aria-label="{{ __('Fechar menu') }}"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 py-4">
        @foreach ($nav as $item)
            @php
                $active = request()->routeIs($item['route']);
            @endphp
            <a
                href="{{ route($item['route']) }}"
                @click="sidebarOpen = false"
                class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                    {{ $active ? 'bg-brand/15 text-white shadow-sm ring-1 ring-brand/25' : 'text-zinc-400 hover:bg-zinc-800/80 hover:text-zinc-100' }}"
            >
                @if ($item['icon'] === 'home')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                @elseif ($item['icon'] === 'pos')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @elseif ($item['icon'] === 'box')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                @elseif ($item['icon'] === 'users')
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                @else
                    <svg class="h-5 w-5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                @endif
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>

    <div class="border-t border-zinc-800 p-3">
        <div class="rounded-xl bg-zinc-950/60 p-3 ring-1 ring-zinc-800">
            <p class="truncate text-sm font-medium text-white">{{ Auth::user()->name }}</p>
            <p class="truncate text-xs text-zinc-500">{{ Auth::user()->email }}</p>
            <div class="mt-3 flex flex-col gap-1">
                <a href="{{ route('profile.edit') }}" class="rounded-lg px-2 py-1.5 text-xs font-medium text-zinc-400 hover:bg-zinc-800 hover:text-white" @click="sidebarOpen = false">{{ __('Perfil') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-lg px-2 py-1.5 text-left text-xs font-medium text-zinc-400 hover:bg-zinc-800 hover:text-red-300">
                        {{ __('Sair') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
