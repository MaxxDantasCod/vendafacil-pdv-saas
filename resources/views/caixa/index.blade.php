<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Caixa - PDV</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; }
        body { overscroll-behavior: none; }
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        kbd { font-family: monospace; font-size: 0.9em; }
      .lista-produto-item.bg-blue-600 { background-color: rgb(37 99 235)!important; }
        [x-cloak] { display: none!important; }
    </style>
</head>

<body x-data="{ modalSangria: false, modalSuprimento: false, menuOpcoesF8: false, opcaoF8: 0 }" class="bg-gray-900 text-gray-100 select-none">

    <!-- Modal Abrir Caixa -->
    @if(!$caixaAberto)
    <div id="modal-abrir-caixa" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4 text-center">Abrir Caixa</h2>
            <p class="text-gray-400 mb-4 text-center">Informe o valor inicial em dinheiro</p>
            <input type="number" id="valor-inicial" step="0.01" min="0" placeholder="0,00"
                   class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 px-4 py-4 text-3xl text-center mb-4"
                   autofocus>
            <button onclick="abrirCaixa()"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg text-xl">
                ENTER - ABRIR CAIXA
            </button>
            <a href="{{ url('/dashboard') }}" class="block text-center mt-4 text-gray-400">Voltar ao Painel</a>
        </div>
    </div>
    @endif
    
<!-- Header Fixo -->
<div class="bg-gray-800 border-b border-gray-700 px-4 py-3">
    <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
            <h4 class="text-xl font-bold m-0">
                CAIXA ABERTO <small class="text-gray-400">[F9=Atalhos]</small>
            </h4>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="abrirSangria()"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold px-4 py-2 rounded-lg text-sm">
                F6 - SANGRIA
            </button>
            <button type="button" onclick="abrirSuprimento()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-lg text-sm">
                F7 - SUPRIMENTO
            </button>
            <button type="button" @click="menuOpcoesF8 = true; opcaoF8 = 0"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold px-4 py-2 rounded-lg text-sm">
                F8 - OPÇÕES
            </button>
            <button type="button" id="btnFechar"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-2 rounded-lg text-sm">
                F10 - FECHAR
            </button>
        </div>
    </div>
</div>

@if(!empty($planLimit))
    @php $remaining = $planLimit - $planUsage; @endphp
    @if($planUsage >= $planLimit)
        <div class="bg-red-900...">
    🚫 LIMITE ATINGIDO ({{ $planUsage }}/{{ $planLimit }}) — 
    <a href="{{ route('planos.index') }}" class="underline text-white">Faça upgrade</a>
</div>
    @elseif($planUsage >= 45)
        <div class="bg-amber-900 border-b-2 border-amber-500 px-4 py-2 text-center text-sm font-bold text-amber-100">
            ⚠️ Faltam {{ $remaining }} vendas ({{ $planUsage }}/{{ $planLimit }})
        </div>
    @endif
@endif

    <!-- Conteúdo Principal -->
    <div class="lg:grid lg:grid-cols-3 lg:gap-4 lg:p-4 p-2">

        <!-- Coluna Busca + Carrinho -->
        <div class="lg:col-span-2 space-y-3">

            <!-- Busca Produto -->
            <div class="bg-gray-800 rounded p-3 relative">
                <label class="block text-sm font-bold mb-1 text-gray-400">F1 - BUSCAR PRODUTO</label>
                <input type="text" id="busca-produto" placeholder="Código ou nome..."
                       class="w-full rounded bg-gray-700 border-2 border-gray-600 px-3 py-3 text-lg"
                       autocomplete="off">
                <div id="resultado-produtos" class="mt-2 border-2 border-gray-600 rounded-md bg-gray-700 shadow-xl hidden max-h-60 overflow-y-auto absolute z-50 w-full left-0"></div>
            </div>

            <!-- Carrinho -->
            <div class="bg-gray-800 rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="tabela-carrinho">
                        <thead class="bg-gray-700">
                            <tr>
                                <th class="px-2 py-2 text-left text-xs lg:text-sm font-bold uppercase">Produto</th>
                                <th class="px-2 py-2 text-center text-xs lg:text-sm font-bold uppercase w-20">Qtd</th>
                                <th class="px-2 py-2 text-right text-xs lg:text-sm font-bold uppercase w-24">Total</th>
                                <th class="px-2 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-600 bg-gray-800" id="carrinho-itens">
                            <tr id="carrinho-vazio">
                                <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                    Nenhum produto
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Coluna Cliente + Total -->
        <div class="lg:col-span-1 space-y-3 mt-3 lg:mt-0">

            <!-- Cliente -->
            <div class="bg-gray-800 rounded-lg p-3 lg:p-4">
                <label class="block text-lg font-bold mb-2">F2 - Cliente</label>
                <input type="text" id="busca-cliente"
                       placeholder="Nome ou CPF..."
                       class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 px-4 py-3 text-lg"
                       autocomplete="off">
                <input type="hidden" id="cliente-id">
                <div id="resultado-clientes" class="mt-2 border-2 border-gray-600 rounded-lg bg-gray-700 shadow-xl hidden max-h-40 overflow-y-auto"></div>
                <div id="cliente-selecionado" class="mt-2 p-2 bg-blue-900 border border-blue-500 rounded-lg text-sm font-bold hidden"></div>
            </div>

            <!-- Totais -->
            <div class="bg-gray-800 rounded-lg p-3 lg:p-4 space-y-3">

                <div class="flex justify-between text-lg">
                    <span class="text-gray-400">Subtotal:</span>
                    <span id="subtotal-venda" class="font-bold">R$ 0,00</span>
                </div>

                <!-- Desconto -->
                <div>
                    <label class="text-xs text-gray-400 uppercase">F3 - DESCONTO</label>
                    <div class="flex gap-2 mt-1">
                        <input type="number" id="desconto-valor" placeholder="Desc" step="0.01" min="0"
                               class="flex-1 rounded-lg border-2 border-gray-600 bg-gray-700 px-3 py-2 text-lg">
                        <select id="desconto-tipo" class="rounded-lg border-2 border-gray-600 bg-gray-700 px-3 py-2 text-lg">
                            <option value="valor">R$</option>
                            <option value="percent">%</option>
                        </select>
                    </div>
                </div>

                <!-- Pagamento -->
                <div>
                    <label class="text-xs text-gray-400 uppercase">F4 - FORMA DE PAGAMENTO</label>
                    <select id="forma-pagamento" class="w-full mt-1 rounded-lg border-2 border-gray-600 bg-gray-700 px-3 py-3 text-lg font-bold">
                        <option value="dinheiro">DINHEIRO</option>
                        <option value="pix">PIX</option>
                        <option value="debito">DÉBITO</option>
                        <option value="credito">CRÉDITO</option>
                    </select>
                </div>

                <!-- Total -->
                <div class="text-center border-t-2 border-gray-600 pt-3">
                    <p class="text-lg text-gray-400">TOTAL</p>
                    <p id="total-venda" class="text-5xl lg:text-6xl font-extrabold text-green-400">R$ 0,00</p>
                </div>

             <!--   <button id="btn-finalizar" onclick="finalizarVenda()" disabled
                        class="w-full rounded-lg bg-green-600 px-4 py-4 text-2xl font-bold disabled:bg-gray-600 disabled:cursor-not-allowed active:bg-green-800">
                    F12 - FINALIZAR
                </button>n-->
                <button type="button" id="btn-finalizar"
    @if(!empty($planLimit) && $planUsage >= $planLimit) disabled @endif
    class="... w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed ...">
    @if(!empty($planLimit) && $planUsage >= $planLimit)
        LIMITE ATINGIDO
    @else
        F12 - FINALIZAR
    @endif
</button>
                <button onclick="limparVenda()"
                        class="w-full rounded-lg bg-red-600 px-4 py-3 text-lg font-bold active:bg-red-800">
                    ESC - CANCELAR
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Atalhos -->
    <div id="modalAtalhos" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-gray-800 rounded-lg p-6 w-96">
                <h3 class="text-xl font-bold text-white mb-4">Atalhos do Teclado</h3>
                <table class="w-full text-gray-300 text-sm">
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F1</kbd></td><td>Buscar Produto</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F2</kbd></td><td>Buscar Cliente</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F3</kbd></td><td>Desconto</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F4</kbd></td><td>Forma Pagamento</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F5</kbd></td><td>Abrir Caixa</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F6</kbd></td><td>Sangria</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F7</kbd></td><td>Suprimento</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F8</kbd></td><td>Menu Opções</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F10</kbd></td><td>Fechar Caixa</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F12</kbd></td><td>Finalizar Venda</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">ESC</kbd></td><td>Cancelar</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">↑ ↓</kbd></td><td>Navegar produtos</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">Enter</kbd></td><td>Add produto</td></tr>
                    <tr><td class="py-1"><kbd class="bg-gray-700 px-2 rounded">F9</kbd></td><td>Ver atalhos</td></tr>
                </table>
                <button onclick="fecharModalAtalhos()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded w-full">Fechar [ESC]</button>
            </div>
        </div>
    </div>

    <!-- Modal Fechar Caixa -->
    <div id="modal-fechar-caixa" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4 hidden">
        <div class="bg-gray-800 rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-bold mb-4 text-center">Fechar Caixa</h2>
            <div id="resumo-caixa" class="space-y-2 mb-4 text-lg"></div>
            <input type="number" id="valor-final" step="0.01" min="0" placeholder="Valor final em dinheiro"
                   class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 px-4 py-3 text-xl mb-3">
            <textarea id="obs-fechamento" placeholder="Observações" rows="2"
                      class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 px-4 py-2 mb-3"></textarea>
            <button onclick="confirmarFecharCaixa()"
                    class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-lg text-xl mb-2">
                CONFIRMAR FECHAMENTO
            </button>
            <button onclick="document.getElementById('modal-fechar-caixa').classList.add('hidden')"
                    class="w-full bg-gray-600 text-white py-2 rounded-lg">
                Cancelar
            </button>
        </div>
    </div>

<!-- Modal Sangria -->
<div x-show="modalSangria" 
     x-transition 
     @keydown.escape.window="modalSangria = false" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
     style="display: none;">
    <div @click.away="modalSangria = false" class="bg-gray-800 rounded-lg p-6 w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-4 text-center">F6 - SANGRIA</h2>
        <div class="mb-4">
            <label class="block text-sm text-gray-400 mb-1">Valor da Sangria</label>
            <input type="number" id="sangria_valor" step="0.01" min="0" placeholder="0,00"
                   class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 px-4 py-3 text-xl"
                   @keydown.enter="confirmarSangria()">
        </div>
        <div class="mb-4">
            <label class="block text-sm text-gray-400 mb-1">Observação</label>
            <input type="text" id="sangria_obs" placeholder="Motivo da retirada"
                   class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 px-4 py-2">
        </div>
        <div class="flex gap-2">
            <button @click="modalSangria = false"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg">
                ESC - Cancelar
            </button>
            <button @click="confirmarSangria()"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-lg">
                ENTER - Confirmar
            </button>
        </div>
    </div>
</div>

<!-- Modal Suprimento -->
<div x-show="modalSuprimento" 
     x-transition 
     @keydown.escape.window="modalSuprimento = false" 
     x-cloak
     class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4"
     style="display: none;">
    <div @click.away="modalSuprimento = false" class="bg-gray-800 rounded-lg p-6 w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-4 text-center">F7 - SUPRIMENTO</h2>
        <div class="mb-4">
            <label class="block text-sm text-gray-400 mb-1">Valor do Suprimento</label>
            <input type="number" id="suprimento_valor" step="0.01" min="0" placeholder="0,00"
                   class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 px-4 py-3 text-xl"
                   @keydown.enter="confirmarSuprimento()">
        </div>
        <div class="mb-4">
            <label class="block text-sm text-gray-400 mb-1">Observação</label>
            <input type="text" id="suprimento_obs" placeholder="Origem do valor"
                   class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 text-gray-100 px-4 py-2">
        </div>
        <div class="flex gap-2">
            <button @click="modalSuprimento = false"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 rounded-lg">
                ESC - Cancelar
            </button>
            <button @click="confirmarSuprimento()"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg">
                ENTER - Confirmar
            </button>
        </div>
    </div>
</div>

<!-- Menu F8 -->
<div x-show="menuOpcoesF8" 
     x-transition
     @keydown.window.escape="menuOpcoesF8 = false"
     @keydown.window.arrow-down.prevent="opcaoF8 = Math.min(opcaoF8 + 1, 2)"
     @keydown.window.arrow-up.prevent="opcaoF8 = Math.max(opcaoF8 - 1, 0)"
     @keydown.window.enter.prevent="executarF8()"
     class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 p-4" 
     style="display: none;">
    <div @click.away="menuOpcoesF8 = false" class="bg-gray-800 rounded-lg p-6 w-full max-w-sm border-2 border-blue-500">
        <h3 class="text-xl font-bold mb-4 text-center">F8 - OPÇÕES</h3>
        <div class="space-y-2">
            <button @click="verRelatorio(); menuOpcoesF8 = false" 
                    :class="opcaoF8 === 0? 'bg-blue-600 ring-2 ring-blue-400' : 'bg-gray-700'"
                    class="w-full text-left px-4 py-3 rounded-lg font-bold hover:bg-blue-600">
                1. RELATÓRIO COMPLETO
            </button>
            <button @click="abrirSangria(); menuOpcoesF8 = false" 
                    :class="opcaoF8 === 1? 'bg-yellow-600 ring-2 ring-yellow-400' : 'bg-gray-700'"
                    class="w-full text-left px-4 py-3 rounded-lg font-bold hover:bg-yellow-600">
                2. SANGRIA
            </button>
            <button @click="abrirSuprimento(); menuOpcoesF8 = false" 
                    :class="opcaoF8 === 2? 'bg-blue-600 ring-2 ring-blue-400' : 'bg-gray-700'"
                    class="w-full text-left px-4 py-3 rounded-lg font-bold hover:bg-blue-600">
                3. SUPRIMENTOS
            </button>
        </div>
        <p class="text-xs text-gray-400 text-center mt-4">↑↓ Navegar | ENTER Confirmar | ESC Cancelar</p>
    </div>
</div>

<script>
let carrinho = [];
let processando = false;
let indiceSelecionado = -1;
const CAIXA_ABERTO = {{ $caixaAberto? 'true' : 'false' }};

console.log('PDV CAIXA CARREGOU');

// Atalhos globais
document.addEventListener('keydown', (e) => {
    if (!CAIXA_ABERTO &&!document.getElementById('modal-abrir-caixa').classList.contains('hidden')) {
        if (e.key === 'Enter') { e.preventDefault(); abrirCaixa(); }
        if (e.key === 'Escape') { e.preventDefault(); window.location.href = '{{ url('/dashboard') }}'; }
        if (e.key === 'F5') { e.preventDefault(); document.getElementById('valor-inicial').focus(); }
        return;
    }

    if (!CAIXA_ABERTO) return;

    if (e.key === 'F1') { e.preventDefault(); document.getElementById('busca-produto').focus(); }
    if (e.key === 'F2') { e.preventDefault(); document.getElementById('busca-cliente').focus(); }
    if (e.key === 'F3') { e.preventDefault(); document.getElementById('desconto-valor').focus(); }
    if (e.key === 'F4') { e.preventDefault(); document.getElementById('forma-pagamento').focus(); }
    if (e.key === 'F6') { e.preventDefault(); abrirSangria(); }
    if (e.key === 'F7') { e.preventDefault(); abrirSuprimento(); }
    if (e.key === 'F8') {
    e.preventDefault();
    Alpine.$data(document.body).menuOpcoesF8 = true;
    Alpine.$data(document.body).opcaoF8 = 0;
}
    if (e.key === 'F9') { e.preventDefault(); abrirModalAtalhos(); }
    if (e.key === 'F10') { e.preventDefault(); fecharCaixa(); }
    if (e.key === 'F12') { e.preventDefault(); if (!document.getElementById('btn-finalizar').disabled) finalizarVenda(); }
    if (e.key === 'Escape') {
        e.preventDefault();
        if (!document.getElementById('modalAtalhos').classList.contains('hidden')) {
            fecharModalAtalhos();
        } else if (!document.getElementById('modal-relatorio').classList.contains('hidden')) {
            fecharModalRelatorio();
        } else if (!processando) {
            limparVenda();
        }
    }
});

function executarF8() {
    const opcao = Alpine.$data(document.body).opcaoF8;
    Alpine.$data(document.body).menuOpcoesF8 = false;
    
    if (opcao === 0) verRelatorio();
    if (opcao === 1) abrirSangria();
    if (opcao === 2) abrirSuprimento();
}

function abrirSangria() {
    Alpine.$data(document.body).modalSangria = true;
    Alpine.$data(document.body).menuOpcoesF8 = false;
    setTimeout(() => document.getElementById('sangria_valor')?.focus(), 100);
}

function abrirSuprimento() {
    Alpine.$data(document.body).modalSuprimento = true;
    Alpine.$data(document.body).menuOpcoesF8 = false;
    setTimeout(() => document.getElementById('suprimento_valor')?.focus(), 100);
}

function confirmarSangria() {
    const valor = document.getElementById('sangria_valor').value;
    const obs = document.getElementById('sangria_obs').value;
    if (!valor || parseFloat(valor) <= 0) {
        alert('Informe um valor válido');
        return;
    }
    fetch('{{ route("caixa.sangria") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ valor: valor, obs: obs })
    })
  .then(res => res.json())
  .then(data => {
        if (data.success) {
            Alpine.$data(document.body).modalSangria = false;
            document.getElementById('sangria_valor').value = '';
            document.getElementById('sangria_obs').value = '';
            alert('Sangria realizada: R$ ' + parseFloat(valor).toFixed(2));
            location.reload();
        } else {
            alert(data.error || 'Erro ao fazer sangria');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erro de rede ao fazer sangria');
    });
}

function confirmarSuprimento() {
    const valor = document.getElementById('suprimento_valor').value;
    const obs = document.getElementById('suprimento_obs').value;
    if (!valor || parseFloat(valor) <= 0) {
        alert('Informe um valor válido');
        return;
    }
    fetch('{{ route("caixa.suprimento") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ valor: valor, obs: obs })
    })
  .then(res => res.json())
  .then(data => {
        if (data.success) {
            Alpine.$data(document.body).modalSuprimento = false;
            document.getElementById('suprimento_valor').value = '';
            document.getElementById('suprimento_obs').value = '';
            alert('Suprimento realizado: R$ ' + parseFloat(valor).toFixed(2));
            location.reload();
        } else {
            alert(data.error || 'Erro ao fazer suprimento');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Erro de rede ao fazer suprimento');
    });
}

// Navegação por setas na busca de produto
document.getElementById('busca-produto')?.addEventListener('keydown', function(e) {
    const itens = document.querySelectorAll('.lista-produto-item');
    if (itens.length === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        indiceSelecionado = Math.min(indiceSelecionado + 1, itens.length - 1);
        atualizarSelecao(itens);
    }
    if (e.key === 'ArrowUp') {
        e.preventDefault();
        indiceSelecionado = Math.max(indiceSelecionado - 1, 0);
        atualizarSelecao(itens);
    }
    if (e.key === 'Enter' && indiceSelecionado >= 0) {
        e.preventDefault();
        itens[indiceSelecionado]?.click();
        indiceSelecionado = -1;
    }
});

function atualizarSelecao(itens) {
    itens.forEach((item, i) => {
        if (i === indiceSelecionado) {
            item.classList.add('bg-blue-600');
            item.scrollIntoView({block: 'nearest'});
        } else {
            item.classList.remove('bg-blue-600');
        }
    });
}

function abrirModalAtalhos() {
    document.getElementById('modalAtalhos').classList.remove('hidden');
}

function fecharModalAtalhos() {
    document.getElementById('modalAtalhos').classList.add('hidden');
}

// Abrir Caixa
async function abrirCaixa() {
    const valor = parseFloat(document.getElementById('valor-inicial').value) || 0;
    const btn = event.target;
    btn.disabled = true;
    btn.textContent = 'ABRINDO...';

    const res = await fetch('{{ route('caixa.abrir') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({valor_inicial: valor})
    });
    if (res.ok) {
        location.reload();
    } else {
        alert('Erro ao abrir caixa');
        btn.disabled = false;
        btn.textContent = 'ABRIR CAIXA';
    }
}

// Fechar Caixa
function fecharCaixa() {
    fetch('{{ route('caixa.relatorio') }}').then(r => r.json()).then(caixa => {
        const resumo = `
            <div class="flex justify-between"><span>Vendas:</span><span class="font-bold">R$ ${parseFloat(caixa.total_vendas).toFixed(2)}</span></div>
            <div class="flex justify-between"><span>Dinheiro:</span><span>R$ ${parseFloat(caixa.total_dinheiro).toFixed(2)}</span></div>
            <div class="flex justify-between"><span>PIX:</span><span>R$ ${parseFloat(caixa.total_pix).toFixed(2)}</span></div>
            <div class="flex justify-between"><span>Débito:</span><span>R$ ${parseFloat(caixa.total_debito).toFixed(2)}</span></div>
            <div class="flex justify-between"><span>Crédito:</span><span>R$ ${parseFloat(caixa.total_credito).toFixed(2)}</span></div>
            <div class="flex justify-between border-t border-gray-600 pt-2"><span>Inicial:</span><span>R$ ${parseFloat(caixa.valor_inicial).toFixed(2)}</span></div>
        `;
        document.getElementById('resumo-caixa').innerHTML = resumo;
        document.getElementById('modal-fechar-caixa').classList.remove('hidden');
        document.getElementById('valor-final').focus();
    });
}

async function confirmarFecharCaixa() {
    const valor = parseFloat(document.getElementById('valor-final').value) || 0;
    const obs = document.getElementById('obs-fechamento').value;
    const res = await fetch('{{ route('caixa.fechar') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({valor_final: valor, obs: obs})
    });
    if (res.ok) {
        alert('Caixa fechado!');
        window.location.href = '{{ url('/dashboard') }}';
    }
}

// Busca Produto
document.getElementById('busca-produto')?.addEventListener('input', async (e) => {
    const termo = e.target.value;
    if (termo.length < 2) {
        document.getElementById('resultado-produtos').classList.add('hidden');
        return;
    }
    const res = await fetch(`{{ route('caixa.buscar-produto') }}?q=${encodeURIComponent(termo)}`);
    const produtos = await res.json();
    const div = document.getElementById('resultado-produtos');
    div.innerHTML = '';
    indiceSelecionado = -1;
    produtos.forEach(p => {
        const item = document.createElement('div');
        item.className = 'lista-produto-item px-4 py-3 hover:bg-gray-600 cursor-pointer border-b border-gray-600 active:bg-gray-500';
        const stockLabel = p.stock_quantity !== null && p.stock_quantity !== undefined ? ` <span class="text-xs text-gray-300">Estoque: ${p.stock_quantity}</span>` : '';
        item.innerHTML = `<strong>${p.ref}</strong> - ${p.label}${stockLabel}<br><span class="text-green-400 font-bold">R$ ${parseFloat(p.price || 0).toFixed(2)}</span>`;
        item.onclick = () => adicionarProduto(p);
        div.appendChild(item);
    });
    div.classList.remove('hidden');
});

function adicionarProduto(produto) {
    const available = produto.stock_quantity;

    if (available !== null && available !== undefined && available <= 0) {
        alert('Produto sem estoque disponível');
        return;
    }

    const existente = carrinho.find(i => i.id === produto.id);
    if (existente) {
        if (available !== null && available !== undefined && existente.qtd + 1 > available) {
            alert('Quantidade maior que o estoque disponível');
            return;
        }
        existente.qtd++;
    } else {
        carrinho.push({
            id: produto.id,
            ref: produto.ref,
            nome: produto.label,
            preco: parseFloat(produto.price || 0),
            qtd: 1
        });
    }
    document.getElementById('busca-produto').value = '';
    document.getElementById('resultado-produtos').classList.add('hidden');
    renderCarrinho();
    document.getElementById('busca-produto').focus();
}

function renderCarrinho() {
    const tbody = document.getElementById('carrinho-itens');
    tbody.innerHTML = '';

    if (carrinho.length === 0) {
        tbody.innerHTML = `<tr id="carrinho-vazio"><td colspan="4" class="px-4 py-8 text-center text-gray-400">Nenhum produto</td></tr>`;
        document.getElementById('subtotal-venda').textContent = 'R$ 0,00';
        document.getElementById('total-venda').textContent = 'R$ 0,00';
        document.getElementById('btn-finalizar').disabled = true;
        return;
    }

    let subtotal = 0;
    carrinho.forEach((item, idx) => {
        const totalItem = item.preco * item.qtd;
        subtotal += totalItem;
        tbody.innerHTML += `
            <tr class="active:bg-gray-700">
                <td class="px-2 py-2 text-sm"><strong>${item.ref}</strong><br><span class="text-xs">${item.nome}</span></td>
                <td class="px-2 py-2 text-center">
                    <input type="number" value="${item.qtd}" min="1"
                           onchange="alterarQtd(${idx}, this.value)"
                           class="w-16 text-center rounded border-2 border-gray-600 bg-gray-700 text-lg font-bold">
                </td>
                <td class="px-2 py-2 text-right text-sm font-bold">R$ ${totalItem.toFixed(2)}</td>
                <td class="px-2 py-2 text-center">
                    <button onclick="removerItem(${idx})" class="text-red-500 text-xl font-bold px-2">X</button>
                </td>
            </tr>
        `;
    });

    document.getElementById('subtotal-venda').textContent = `R$ ${subtotal.toFixed(2)}`;
    calcularTotal(subtotal);
    document.getElementById('btn-finalizar').disabled = false;
}

function calcularTotal(subtotal) {
    const descontoValor = parseFloat(document.getElementById('desconto-valor').value) || 0;
    const descontoTipo = document.getElementById('desconto-tipo').value;
    let total = subtotal;
    if (descontoTipo === 'valor') {
        total = subtotal - descontoValor;
    } else {
        total = subtotal - (subtotal * descontoValor / 100);
    }
    document.getElementById('total-venda').textContent = `R$ ${Math.max(0, total).toFixed(2)}`;
}

document.getElementById('desconto-valor')?.addEventListener('input', () => {
    const subtotal = carrinho.reduce((acc, item) => acc + (item.preco * item.qtd), 0);
    calcularTotal(subtotal);
});

document.getElementById('desconto-tipo')?.addEventListener('change', () => {
    const subtotal = carrinho.reduce((acc, item) => acc + (item.preco * item.qtd), 0);
    calcularTotal(subtotal);
});

function alterarQtd(idx, qtd) {
    carrinho[idx].qtd = parseInt(qtd) || 1;
    renderCarrinho();
}

function removerItem(idx) {
    carrinho.splice(idx, 1);
    renderCarrinho();
}

function limparVenda() {
    if (processando) return;
    if (confirm('Cancelar venda?')) {
        carrinho = [];
        document.getElementById('cliente-id').value = '';
        document.getElementById('cliente-selecionado').classList.add('hidden');
        document.getElementById('desconto-valor').value = '';
        renderCarrinho();
        document.getElementById('busca-produto').focus();
    }
}

// Busca Cliente
document.getElementById('busca-cliente')?.addEventListener('input', async (e) => {
    const termo = e.target.value;
    if (termo.length < 2) {
        document.getElementById('resultado-clientes').classList.add('hidden');
        return;
    }
    const res = await fetch(`{{ route('caixa.buscar-cliente') }}?q=${encodeURIComponent(termo)}`);
    const clientes = await res.json();
    const div = document.getElementById('resultado-clientes');
    div.innerHTML = '';
    clientes.forEach(c => {
        const item = document.createElement('div');
        item.className = 'px-4 py-2 hover:bg-gray-600 cursor-pointer border-b border-gray-600 active:bg-gray-500';
        item.textContent = `${c.name} - ${c.email || ''}`;
        item.onclick = () => selecionarCliente(c);
        div.appendChild(item);
    });
    div.classList.remove('hidden');
});

function selecionarCliente(cliente) {
    document.getElementById('cliente-id').value = cliente.id;
    document.getElementById('busca-cliente').value = '';
    document.getElementById('resultado-clientes').classList.add('hidden');
    document.getElementById('cliente-selecionado').textContent = cliente.name;
    document.getElementById('cliente-selecionado').classList.remove('hidden');
}

// Finalizar Venda
async function finalizarVenda() {
    if (carrinho.length === 0) return;
    const btn = document.getElementById('btn-finalizar');
    btn.disabled = true;
    btn.textContent = 'PROCESSANDO...';
    processando = true;

    try {
        const res = await fetch('{{ route('caixa.finalizar') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                carrinho: carrinho,
                cliente_id: document.getElementById('cliente-id').value || null,
                desconto: parseFloat(document.getElementById('desconto-valor').value) || 0,
                desconto_tipo: document.getElementById('desconto-tipo').value,
                forma_pagamento: document.getElementById('forma-pagamento').value
            })
        });

        const data = await res.json();
        if (data.success) {
            alert(`Venda ${data.invoice_id} finalizada!`);
            carrinho = [];
            document.getElementById('cliente-id').value = '';
            document.getElementById('cliente-selecionado').classList.add('hidden');
            document.getElementById('desconto-valor').value = '';
            renderCarrinho();
            document.getElementById('busca-produto').focus();
        } else {
            alert('Erro: ' + (data.error || 'Desconhecido'));
        }
    } catch (e) {
        alert('Erro de rede');
    } finally {
        btn.disabled = false;
        btn.textContent = 'F12 - FINALIZAR';
        processando = false;
    }
}

// Relatório bonito F8
function verRelatorio() {
    fetch('{{ route('caixa.relatorio') }}').then(r => r.json()).then(c => {
        if (c) {
            const dataAbertura = new Date(c.aberto_em).toLocaleString('pt-BR');
            const html = `
                <div class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4" id="modal-relatorio">
                    <div class="bg-gray-800 rounded-lg p-6 w-full max-w-lg">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-bold text-green-400">RELATÓRIO DO CAIXA</h2>
                            <button onclick="fecharModalRelatorio()" class="text-gray-400 hover:text-white text-2xl">&times;</button>
                        </div>
                        
                        <div class="space-y-3 text-lg">
                            <div class="bg-gray-700 rounded p-3">
                                <div class="text-xs text-gray-400">ABERTO EM</div>
                                <div class="font-bold">${dataAbertura}</div>
                            </div>
                            
                            <div class="bg-gray-700 rounded p-3">
                                <div class="text-xs text-gray-400">VALOR INICIAL</div>
                                <div class="font-bold text-blue-400">R$ ${parseFloat(c.valor_inicial).toFixed(2)}</div>
                            </div>

                            <div class="border-t border-gray-600 pt-3">
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-400">Total Vendas:</span>
                                    <span class="font-bold text-green-400">R$ ${parseFloat(c.total_vendas).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Dinheiro:</span>
                                    <span>R$ ${parseFloat(c.total_dinheiro).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">PIX:</span>
                                    <span>R$ ${parseFloat(c.total_pix).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Débito:</span>
                                    <span>R$ ${parseFloat(c.total_debito).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Crédito:</span>
                                    <span>R$ ${parseFloat(c.total_credito).toFixed(2)}</span>
                                </div>
                            </div>

                            <div class="border-t border-gray-600 pt-3">
                                <div class="flex justify-between text-sm text-gray-400">
                                    <span>Sangrias:</span>
                                    <span>R$ ${parseFloat(c.total_sangria || 0).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-400">
                                    <span>Suprimentos:</span>
                                    <span>R$ ${parseFloat(c.total_suprimento || 0).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>

                        <button onclick="fecharModalRelatorio()" class="mt-6 w-full bg-blue-600 text-white py-3 rounded-lg font-bold">
                            FECHAR [ESC]
                        </button>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
        }
    });
}

function fecharModalRelatorio() {
    document.getElementById('modal-relatorio')?.remove();
}

// Foca na busca ao carregar
if (CAIXA_ABERTO) {
    document.getElementById('busca-produto')?.focus();
} else {
    document.getElementById('valor-inicial')?.focus();
}
</script>

</body>
</html>