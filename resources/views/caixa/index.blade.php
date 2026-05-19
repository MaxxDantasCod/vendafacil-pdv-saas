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
    </style>
</head>
<body class="bg-gray-900 text-gray-100 select-none">
    
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
                ABRIR CAIXA
            </button>
            <a href="{{ url('/dashboard') }}" class="block text-center mt-4 text-gray-400">Voltar ao Painel</a>
        </div>
    </div>
    @endif

    <!-- Header Fixo -->
    <div class="bg-gray-800 border-b-2 border-gray-700 sticky top-0 z-40">
        <div class="flex items-center justify-between px-3 py-2">
            <div class="flex items-center gap-2">
                <button onclick="toggleFullscreen()" class="p-2 hover:bg-gray-700 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                </button>
                <span class="text-sm font-bold text-green-400">CAIXA ABERTO</span>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="verRelatorio()" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-bold">
                    RELATÓRIO
                </button>
                <button onclick="fecharCaixa()" class="px-3 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-bold">
                    FECHAR
                </button>
            </div>
        </div>
    </div>

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
                <div class="flex gap-2">
                    <input type="number" id="desconto-valor" placeholder="Desc" step="0.01" min="0"
                           class="flex-1 rounded-lg border-2 border-gray-600 bg-gray-700 px-3 py-2 text-lg">
                    <select id="desconto-tipo" class="rounded-lg border-2 border-gray-600 bg-gray-700 px-3 py-2 text-lg">
                        <option value="valor">R$</option>
                        <option value="percent">%</option>
                    </select>
                </div>

                <!-- Pagamento -->
                <select id="forma-pagamento" class="w-full rounded-lg border-2 border-gray-600 bg-gray-700 px-3 py-3 text-lg font-bold">
                    <option value="dinheiro">DINHEIRO</option>
                    <option value="pix">PIX</option>
                    <option value="debito">DÉBITO</option>
                    <option value="credito">CRÉDITO</option>
                </select>

                <!-- Total -->
                <div class="text-center border-t-2 border-gray-600 pt-3">
                    <p class="text-lg text-gray-400">TOTAL</p>
                    <p id="total-venda" class="text-5xl lg:text-6xl font-extrabold text-green-400">R$ 0,00</p>
                </div>
                
                <button id="btn-finalizar" onclick="finalizarVenda()" disabled
                        class="w-full rounded-lg bg-green-600 px-4 py-4 text-2xl font-bold disabled:bg-gray-600 disabled:cursor-not-allowed active:bg-green-800">
                    F12 - FINALIZAR
                </button>
                <button onclick="limparVenda()" 
                        class="w-full rounded-lg bg-red-600 px-4 py-3 text-lg font-bold active:bg-red-800">
                    ESC - CANCELAR
                </button>
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

<script>
let carrinho = [];
let processando = false;
const CAIXA_ABERTO = {{ $caixaAberto ? 'true' : 'false' }};

// Atalhos
document.addEventListener('keydown', (e) => {
    if (!CAIXA_ABERTO) return;
    if (e.key === 'F1') { e.preventDefault(); document.getElementById('busca-produto').focus(); }
    if (e.key === 'F2') { e.preventDefault(); document.getElementById('busca-cliente').focus(); }
    if (e.key === 'F12') { e.preventDefault(); if (!document.getElementById('btn-finalizar').disabled) finalizarVenda(); }
    if (e.key === 'Escape') { e.preventDefault(); if (!processando) limparVenda(); }
});

// Abrir Caixa
async function abrirCaixa() {
    const valor = parseFloat(document.getElementById('valor-inicial').value) || 0;
    const res = await fetch('{{ route('caixa.abrir') }}', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({valor_inicial: valor})
    });
    if (res.ok) location.reload();
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

// Tela Cheia
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

// Busca Produto
document.getElementById('busca-produto')?.addEventListener('input', async (e) => {
    const termo = e.target.value;
    if (termo.length < 2) {
        document.getElementById('resultado-produtos').classList.add('hidden');
        return;
    }
    const res = await fetch(`{{ route('caixa.buscar-produto') }}?q=${termo}`);
    const produtos = await res.json();
    const div = document.getElementById('resultado-produtos');
    div.innerHTML = '';
    produtos.forEach(p => {
        const item = document.createElement('div');
        item.className = 'px-4 py-3 hover:bg-gray-600 cursor-pointer border-b border-gray-600 active:bg-gray-500';
        item.innerHTML = `<strong>${p.ref}</strong> - ${p.label}<br><span class="text-green-400 font-bold">R$ ${parseFloat(p.price || 0).toFixed(2)}</span>`;
        item.onclick = () => adicionarProduto(p);
        div.appendChild(item);
    });
    div.classList.remove('hidden');
});

function adicionarProduto(produto) {
    const existente = carrinho.find(i => i.id === produto.id);
    if (existente) {
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
    const res = await fetch(`{{ route('caixa.buscar-cliente') }}?q=${termo}`);
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

function verRelatorio() {
    fetch('{{ route('caixa.relatorio') }}').then(r => r.json()).then(c => {
        if (c) {
            alert(`CAIXA DO DIA\n\nVendas: R$ ${parseFloat(c.total_vendas).toFixed(2)}\nDinheiro: R$ ${parseFloat(c.total_dinheiro).toFixed(2)}\nPIX: R$ ${parseFloat(c.total_pix).toFixed(2)}\nDébito: R$ ${parseFloat(c.total_debito).toFixed(2)}\nCrédito: R$ ${parseFloat(c.total_credito).toFixed(2)}`);
        }
    });
}

// Foca na busca ao carregar
if (CAIXA_ABERTO) {
    document.getElementById('busca-produto')?.focus();
}
</script>

</body>
</html>