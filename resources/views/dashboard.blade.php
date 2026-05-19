<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold tracking-tight text-white">{{ __('Dashboard') }}</h1>
            @isset($planLabel)
                <span class="inline-flex w-fit items-center rounded-full border border-brand/30 bg-brand/10 px-3 py-1 text-xs font-medium text-brand-muted">
                    {{ __('Plano') }}: {{ $planLabel }}
                </span>
            @endisset
        </div>
    </x-slot>

    @php
        $money = fn (?int $cents) => $cents === null ? '—' : 'R$ '.number_format($cents / 100, 2, ',', '.');
    @endphp

    <div class="mx-auto max-w-7xl space-y-8">
        @if (empty($tenant))
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/5 px-4 py-3 text-sm text-amber-100">
                {{ __('Nenhum tenant encontrado para o seu e-mail. Cadastre um registro na tabela tenants com o mesmo e-mail para métricas e API.') }}
            </div>
        @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5 transition hover:border-zinc-700">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-brand/10 blur-2xl"></div>
                <p class="text-sm font-medium text-zinc-400">{{ __('Vendas hoje') }}</p>
                <p class="mt-4 text-4xl font-semibold tabular-nums tracking-tight text-white">{{ number_format($salesToday ?? 0, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-zinc-500">{{ __('Faturas com data de hoje (Dolibarr)') }}</p>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5 transition hover:border-zinc-700">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl"></div>
                <p class="text-sm font-medium text-zinc-400">{{ __('Faturamento no mês') }}</p>
                <p class="mt-4 text-4xl font-semibold tabular-nums tracking-tight text-white">{{ $money($revenueMonthCents ?? 0) }}</p>
                <p class="mt-2 text-xs text-zinc-500">{{ __('Soma das faturas no mês corrente') }}</p>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5 transition hover:border-zinc-700 sm:col-span-2 xl:col-span-1">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-violet-500/10 blur-2xl"></div>
                <p class="text-sm font-medium text-zinc-400">{{ __('Ticket médio') }}</p>
                <p class="mt-4 text-4xl font-semibold tabular-nums tracking-tight text-white">{{ $money($averageTicketCents ?? 0) }}</p>
                <p class="mt-2 text-xs text-zinc-500">{{ __('Média por fatura no mês') }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a
                href="{{ route('caixa.index') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand/25 transition hover:bg-brand-muted focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 focus:ring-offset-zinc-950"
            >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                {{ __('Abrir PDV') }}
            </a>
            <a
                href="{{ route('produtos.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-200 transition hover:border-zinc-600 hover:bg-zinc-800"
            >
                {{ __('Ver produtos') }}
            </a>
        </div>
    </div>
</x-app-layout>
