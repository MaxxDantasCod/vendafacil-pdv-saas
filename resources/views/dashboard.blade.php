<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-xl font-semibold tracking-tight text-white">{{ __('Dashboard') }}</h1>
            <!-- DEBUG -->
{{-- {{ dd($planLabel, $planUsage, $planLimit) }} --}}
            @isset($planLabel)
    <span class="inline-flex w-fit items-center rounded-full border border-brand/30 bg-brand/10 px-3 py-1 text-xs font-medium text-brand-muted">
        {{ __('Plano') }}: {{ $planLabel }}
        @if(!empty($planLimit))
            - {{ $planUsage }}/{{ $planLimit }} vendas
        @endif
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

    @if(!empty($planLimit))
        @php $remaining = $planLimit - ($planUsage ?? 0); @endphp
        
        @if($planUsage >= $planLimit)
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-100 flex items-center justify-between">
                <span>🚫 {{ __('Limite do plano Free atingido') }} ({{ $planUsage }}/{{ $planLimit }}). {{ __('Faça upgrade para continuar vendendo.') }}</span>
                <a href="{{ route('planos.index') }}" class="ml-4 inline-flex items-center rounded-lg bg-white/10 px-3 py-1.5 text-xs font-medium hover:bg-white/20">{{ __('Fazer Upgrade') }}</a>
            </div>
        @elseif($planUsage >= 45)
            <div class="rounded-2xl border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-sm text-amber-100 flex items-center justify-between">
                <span>⚠️ {{ __('Atenção') }}: {{ __('faltam apenas') }} {{ $remaining }} {{ __('vendas no plano Free') }} ({{ $planUsage }}/{{ $planLimit }}).</span>
                <a href="{{ route('planos.index') }}" class="ml-4 inline-flex items-center rounded-lg bg-white/10 px-3 py-1.5 text-xs font-medium hover:bg-white/20">{{ __('Ver planos') }}</a>
            </div>
        @endif
    @endif

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5 transition hover:border-zinc-700">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-brand/10 blur-2xl"></div>
                <p class="text-sm font-medium text-zinc-400">{{ __('Vendas hoje') }}</p>
                <p class="mt-4 text-4xl font-semibold tabular-nums tracking-tight text-white">{{ $money($salesTodayAmountCents ?? 0) }}</p>
                <p class="mt-2 text-xs text-zinc-500">{{ ($salesTodayCount ?? 0) . ' ' . __('vendas hoje') }} · {{ __('fonte') }}: {{ __('Local') }}</p>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5 transition hover:border-zinc-700">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-emerald-500/10 blur-2xl"></div>
                <p class="text-sm font-medium text-zinc-400">{{ __('Faturamento no mês') }}</p>
                <p class="mt-4 text-4xl font-semibold tabular-nums tracking-tight text-white">{{ $money($salesMonthAmountCents ?? 0) }}</p>
                <p class="mt-2 text-xs text-zinc-500">{{ ($salesMonthCount ?? 0) . ' ' . __('faturas no mês') }}</p>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-zinc-800 bg-gradient-to-br from-zinc-900 to-zinc-950 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5 transition hover:border-zinc-700">
                <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-sky-500/10 blur-2xl"></div>
                <p class="text-sm font-medium text-zinc-400">{{ __('Estoque disponível') }}</p>
                <p class="mt-4 text-4xl font-semibold tabular-nums tracking-tight text-white">{{ number_format($inventoryCount ?? 0, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-zinc-500">{{ __('Unidades no estoque local') }}</p>
                @if(!empty($lowStockCount))
                    <p class="mt-2 text-xs text-amber-400">{{ $lowStockCount }} {{ __('produtos com estoque baixo') }}</p>
                @endif
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
            <a
                href="{{ route('estoque.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-zinc-700 bg-zinc-900 px-5 py-2.5 text-sm font-medium text-zinc-200 transition hover:border-zinc-600 hover:bg-zinc-800"
            >
                {{ __('Gerenciar estoque') }}
            </a>
        </div>
    </div>
</x-app-layout>
