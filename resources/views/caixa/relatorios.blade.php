<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-white">{{ __('Relatórios de Caixa') }}</h1>
        </div>
    </x-slot>

    @php $money = fn($cents) => $cents === null ? '—' : 'R$ '.number_format($cents/100, 2, ',', '.'); @endphp

    <div class="mx-auto max-w-6xl py-6 space-y-6">
        <div class="rounded-3xl border border-zinc-800 bg-zinc-950/70 p-5 shadow-xl shadow-black/20 ring-1 ring-white/5">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Relatório de caixa</p>
                    <h2 class="mt-2 text-2xl font-semibold text-white">Caixas do dia</h2>
                    <p class="mt-1 text-sm text-zinc-400">Somente os caixas abertos hoje são carregados automaticamente. Para outras datas, altere o período e clique em Buscar.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-[auto_auto] lg:grid-cols-[auto_auto_auto_auto]">
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-zinc-400">De</label>
                        <input type="date" id="start" value="{{ today()->toDateString() }}" class="rounded-2xl border border-zinc-800 bg-zinc-900 px-3 py-2 text-white outline-none focus:border-brand focus:ring-2 focus:ring-brand/20" />
                    </div>
                    <div class="space-y-1">
                        <label class="block text-xs font-medium text-zinc-400">Até</label>
                        <input type="date" id="end" value="{{ today()->toDateString() }}" class="rounded-2xl border border-zinc-800 bg-zinc-900 px-3 py-2 text-white outline-none focus:border-brand focus:ring-2 focus:ring-brand/20" />
                    </div>
                    <div class="flex items-end">
                        <button id="buscar" class="rounded-2xl bg-brand px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand/20 transition hover:bg-brand-muted">Buscar</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="result" class="grid gap-4"></div>
    </div>

    <script>
        function formatDateTime(value) {
            if (!value) {
                return '—';
            }
            const date = new Date(value);
            return date.toLocaleString('pt-BR', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        }

        function formatMoney(value) {
            return 'R$ ' + Number(value || 0).toFixed(2).replace('.', ',');
        }

        async function fetchCaixas() {
            const start = document.getElementById('start').value;
            const end = document.getElementById('end').value;
            const params = new URLSearchParams();
            if (start) params.append('start', start);
            if (end) params.append('end', end);

            const res = await fetch('/caixa/buscar-caixas?' + params.toString(), { headers: { 'Accept': 'application/json' } });
            if (!res.ok) {
                document.getElementById('result').innerHTML = '<div class="rounded-2xl border border-red-600/30 bg-red-600/10 p-4 text-sm text-red-200">Erro ao buscar caixas.</div>';
                return;
            }

            const caixas = await res.json();
            renderCaixas(caixas);
        }

        function renderCaixas(caixas) {
            const el = document.getElementById('result');
            if (!Array.isArray(caixas) || caixas.length === 0) {
                el.innerHTML = '<div class="rounded-2xl border border-zinc-800 bg-zinc-950/70 p-6 text-sm text-zinc-400">Nenhum caixa encontrado para o período selecionado.</div>';
                return;
            }

            el.innerHTML = '';
            caixas.forEach(c => {
                const aberto = formatDateTime(c.aberto_em);
                const fechado = c.fechado_em ? formatDateTime(c.fechado_em) : 'Aberto';
                const dinheiro = formatMoney(c.total_dinheiro || 0);
                const pix = formatMoney(c.total_pix || 0);
                const debito = formatMoney(c.total_debito || 0);
                const credito = formatMoney(c.total_credito || 0);
                const sangria = formatMoney(c.total_sangria || 0);
                const suprimento = formatMoney(c.total_suprimento || 0);
                const totalVendas = formatMoney(c.total_vendas || ((c.total_dinheiro || 0) + (c.total_pix || 0) + (c.total_debito || 0) + (c.total_credito || 0)));
                const valorFechamento = c.valor_final !== null ? formatMoney(c.valor_final) : '—';
                const valorEsperado = formatMoney((c.valor_inicial || 0) + (c.total_dinheiro || 0) + (c.total_suprimento || 0) - (c.total_sangria || 0));
                const operador = c.user?.name || '—';
                const statusLabel = c.status === 'fechado' ? 'Fechado' : 'Aberto';

                const div = document.createElement('div');
                div.className = 'rounded-[32px] border border-zinc-800 bg-zinc-950/90 p-6 shadow-xl shadow-black/20 ring-1 ring-white/5';
                div.innerHTML = `
                    <div class="flex flex-col gap-5 xl:flex-row xl:items-start xl:justify-between">
                        <div class="space-y-4 xl:w-2/3">
                            <div class="flex flex-wrap items-center gap-3">
                                <span class="rounded-full bg-brand/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-brand-muted">Caixa #${c.id}</span>
                                <span class="rounded-full bg-zinc-800 px-3 py-1 text-xs font-medium text-zinc-300">${statusLabel}</span>
                            </div>
                            <div class="grid gap-3 lg:grid-cols-3">
                                <div class="rounded-2xl bg-zinc-900/80 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Valor caixa</p>
                                    <p class="mt-2 text-2xl font-semibold text-white">${valorFechamento}</p>
                                    <p class="mt-1 text-sm text-zinc-400">Fechamento registrado</p>
                                </div>
                                <div class="rounded-2xl bg-zinc-900/80 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Relatório</p>
                                    <p class="mt-2 text-2xl font-semibold text-white">${valorEsperado}</p>
                                    <p class="mt-1 text-sm text-zinc-400">Saldo esperado no caixa</p>
                                </div>
                                <div class="rounded-2xl bg-zinc-900/80 p-4">
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Vendas</p>
                                    <p class="mt-2 text-2xl font-semibold text-white">${totalVendas}</p>
                                    <p class="mt-1 text-sm text-zinc-400">${c.invoices_count || 0} vendas</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-zinc-800 bg-zinc-900/90 p-4 text-sm text-zinc-300 xl:w-1/3">
                            <div class="space-y-3">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Operador</p>
                                    <p class="mt-1 text-white">${operador}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Abertura</p>
                                    <p class="mt-1 text-white">${aberto}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Fechamento</p>
                                    <p class="mt-1 text-white">${fechado}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Sangria</p>
                                    <p class="mt-1 text-white">${sangria}</p>
                                </div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Suprimento</p>
                                    <p class="mt-1 text-white">${suprimento}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl bg-zinc-900/80 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Dinheiro</p>
                            <p class="mt-2 text-lg font-semibold text-white">${dinheiro}</p>
                        </div>
                        <div class="rounded-2xl bg-zinc-900/80 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Pix</p>
                            <p class="mt-2 text-lg font-semibold text-white">${pix}</p>
                        </div>
                        <div class="rounded-2xl bg-zinc-900/80 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Débito</p>
                            <p class="mt-2 text-lg font-semibold text-white">${debito}</p>
                        </div>
                        <div class="rounded-2xl bg-zinc-900/80 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-zinc-500">Crédito</p>
                            <p class="mt-2 text-lg font-semibold text-white">${credito}</p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button data-caixa="${c.id}" class="ver-vendas inline-flex items-center justify-center rounded-2xl bg-brand px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-muted">Ver vendas</button>
                    </div>
                `;
                el.appendChild(div);
            });

            document.querySelectorAll('.ver-vendas').forEach(btn => btn.addEventListener('click', async (e) => {
                const id = e.currentTarget.getAttribute('data-caixa');
                await fetchVendas(id);
            }));
        }

        async function fetchVendas(caixaId) {
            const res = await fetch(`/caixa/${caixaId}/vendas`, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) {
                alert('Erro ao carregar vendas');
                return;
            }
            const invoices = await res.json();
            let html = '<div class="space-y-4">';
            invoices.forEach(inv => {
                const payment = inv.forma_pagamento || '—';
                const date = formatDateTime(inv.invoice_date || inv.created_at);
                html += `<div class="rounded-3xl border border-zinc-800 bg-zinc-950/80 p-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <span class="text-sm text-zinc-400">Fatura #${inv.id}</span>
                            <span class="text-sm font-semibold text-white">${formatMoney(inv.total)}</span>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            <div class="text-sm text-zinc-400">${date}</div>
                            <div class="text-sm text-zinc-400">Forma: ${payment}</div>
                        </div>
                    </div>`;
                if (inv.items && inv.items.length) {
                    html += '<div class="mt-4 space-y-2 text-sm text-zinc-300">';
                    inv.items.forEach(it => {
                        const itemTotal = Number(it.qtd || 0) * Number(it.preco || 0);
                        html += `<div class="rounded-2xl bg-zinc-900/80 p-3 flex items-center justify-between">
                                    <span>${it.nome} — ${it.qtd}× R$ ${Number(it.preco).toFixed(2).replace('.', ',')}</span>
                                    <span class="font-medium text-white">R$ ${itemTotal.toFixed(2).replace('.', ',')}</span>
                                </div>`;
                    });
                    html += '</div>';
                }
                html += '</div>';
            });
            html += '</div>';

            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-start justify-center p-6';
            modal.innerHTML = `
                <div class="absolute inset-0 bg-black/70"></div>
                <div class="relative w-full max-w-4xl overflow-hidden rounded-[32px] border border-zinc-800 bg-zinc-950/95 shadow-2xl shadow-black/40">
                    <div class="p-6">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-xl font-semibold text-white">Vendas do caixa ${caixaId}</h3>
                            <button id="closeModal" class="rounded-2xl bg-zinc-800 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-700">Fechar</button>
                        </div>
                        <div class="mt-6">${html}</div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            document.getElementById('closeModal').addEventListener('click', () => modal.remove());
        }

        document.getElementById('buscar').addEventListener('click', fetchCaixas);
        window.addEventListener('DOMContentLoaded', fetchCaixas);
    </script>
</x-app-layout>