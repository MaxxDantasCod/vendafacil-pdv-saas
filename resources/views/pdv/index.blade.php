<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">
            {{ __('PDV - Frente de Caixa') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-4">
                
                <!-- Coluna 1: Busca e Carrinho -->
                <div class="col-span-2">
                    <div class="bg-gray-800 shadow-sm rounded-lg p-6">
                        
                        <!-- Busca Produto -->
                        <div class="mb-4">
                            <label class="block text-lg font-bold text-gray-100 mb-2">F1 - Buscar Produto</label>
                            <input type="text" id="busca-produto" 
                                   placeholder="Código ou nome..."
                                   class="w-full rounded-md border-2 border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 px-4 py-3 text-xl"
                                   autocomplete="off" autofocus>
                            <div id="resultado-produtos" class="mt-2 border-2 border-gray-600 rounded-md bg-gray-700 shadow-xl hidden max-h-60 overflow-y-auto"></div>
                        </div>

                        <!-- Tabela Carrinho -->
                        <div class="overflow-hidden rounded-lg border-2 border-gray-600">
                            <table class="min-w-full" id="tabela-carrinho">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-bold uppercase text-gray-100">Produto</th>
                                        <th class="px-4 py-3 text-center text-sm font-bold uppercase text-gray-100 w-24">Qtd</th>
                                        <th class="px-4 py-3 text-right text-sm font-bold uppercase text-gray-100 w-32">Preço</th>
                                        <th class="px-4 py-3 text-right text-sm font-bold uppercase text-gray-100 w-32">Total</th>
                                        <th class="px-4 py-3 w-16"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-600 bg-gray-800" id="carrinho-itens">
                                    <tr id="carrinho-vazio">
                                        <td colspan="5" class="px-4 py-12 text-center text-lg text-gray-400">
                                            Nenhum produto adicionado
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Coluna 2: Cliente e Total -->
                <div class="col-span-1">
                    <div class="bg-gray-800 shadow-sm rounded-lg p-6">
                        
                        <!-- Busca Cliente -->
                        <div class="mb-4">
                            <label class="block text-lg font-bold text-gray-100 mb-2">F2 - Cliente</label>
                            <input type="text" id="busca-cliente" 
                                   placeholder="Nome ou email..."
                                   class="w-full rounded-md border-2 border-gray-600 bg-gray-700 text-gray-100 placeholder-gray-400 px-4 py-3 text-lg"
                                   autocomplete="off">
                            <input type="hidden" id="cliente-id">
                            <div id="resultado-clientes" class="mt-2 border-2 border-gray-600 rounded-md bg-gray-700 shadow-xl hidden max-h-40 overflow-y-auto"></div>
                            <div id="cliente-selecionado" class="mt-3 p-3 bg-blue-900 border-2 border-blue-500 rounded-md text-lg font-bold text-blue-100 hidden"></div>
                        </div>

                        <!-- Total + Desconto + Pagamento -->
                        <div class="border-t-2 border-gray-600 pt-6 mt-6 space-y-4">
                            
                            <!-- Subtotal -->
                            <div class="flex justify-between text-lg text-gray-300">
                                <span>Subtotal:</span>
                                <span id="subtotal-venda" class="font-bold text-gray-100">R$ 0,00</span>
                            </div>

                            <!-- Desconto -->
                            <div class="flex gap-2">
                                <input type="number" id="desconto-valor" placeholder="Desconto" step="0.01" min="0"
                                       class="flex-1 rounded-md border-2 border-gray-600 bg-gray-700 text-gray-100 px-3 py-2 text-lg">
                                <select id="desconto-tipo" class="rounded-md border-2 border-gray-600 bg-gray-700 text-gray-100 px-3 py-2 text-lg">
                                    <option value="valor">R$</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>

                            <!-- Forma Pagamento -->
                            <div>
                                <label class="block text-sm font-bold text-gray-300 mb-1">Forma de Pagamento</label>
                                <select id="forma-pagamento" class="w-full rounded-md border-2 border-gray-600 bg-gray-700 text-gray-100 px-3 py-2 text-lg">
                                    <option value="dinheiro">Dinheiro</option>
                                    <option value="pix">PIX</option>
                                    <option value="debito">Cartão Débito</option>
                                    <option value="credito">Cartão Crédito</option>
                                </select>
                            </div>

                            <!-- Total Final -->
                            <div class="text-center border-t-2 border-gray-600 pt-4">
                                <p class="text-xl text-gray-300">TOTAL</p>
                                <p id="total-venda" class="text-6xl font-extrabold text-green-400">R$ 0,00</p>
                            </div>
                            
                            <button id="btn-finalizar" onclick="finalizarVenda()" disabled
                                    class="w-full rounded-lg bg-green-600 px-4 py-4 text-xl font-bold text-white disabled:bg-gray-600 disabled:cursor-not-allowed hover:bg-green-700">
                                F12 - FINALIZAR
                            </button>
                            <button id="btn-cancelar" onclick="limparVenda()" 
                                    class="w-full mt-2 rounded-lg bg-red-600 px-4 py-3 text-lg font-bold text-white hover:bg-red-700">
                                ESC - CANCELAR
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        let carrinho = [];
        let processando = false;
        
        document.addEventListener('keydown', (e) => {
            if (e.key === 'F1') { e.preventDefault(); document.getElementById('busca-produto').focus(); }
            if (e.key === 'F2') { e.preventDefault(); document.getElementById('busca-cliente').focus(); }
            if (e.key === 'F12') { e.preventDefault(); if (!document.getElementById('btn-finalizar').disabled) finalizarVenda(); }
            if (e.key === 'Escape') { e.preventDefault(); if (!processando) limparVenda(); }
        });

        document.getElementById('busca-produto').addEventListener('input', async (e) => {
            const termo = e.target.value;
            if (termo.length < 2) {
                document.getElementById('resultado-produtos').classList.add('hidden');
                return;
            }
            
            const res = await fetch(`{{ route('pdv.buscar-produto') }}?q=${termo}`);
            const produtos = await res.json();
            
            const div = document.getElementById('resultado-produtos');
            div.innerHTML = '';
            
            if (produtos.length === 0) {
                div.classList.add('hidden');
                return;
            }
            
            produtos.forEach(p => {
                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-gray-600 cursor-pointer text-lg text-gray-100 border-b border-gray-600';
                item.innerHTML = `<strong class="text-white">${p.ref}</strong> - ${p.label}<br><span class="text-green-400 font-bold">R$ ${parseFloat(p.price || 0).toFixed(2)}</span>`;
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
        }

        function renderCarrinho() {
            const tbody = document.getElementById('carrinho-itens');
            tbody.innerHTML = '';
            
            if (carrinho.length === 0) {
                tbody.innerHTML = `<tr id="carrinho-vazio">
                    <td colspan="5" class="px-4 py-12 text-center text-lg text-gray-400">Nenhum produto adicionado</td>
                </tr>`;
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
                    <tr class="hover:bg-gray-700">
                        <td class="px-4 py-3 text-lg text-gray-100"><strong class="text-white">${item.ref}</strong><br>${item.nome}</td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" value="${item.qtd}" min="1" 
                                   onchange="alterarQtd(${idx}, this.value)"
                                   class="w-20 text-center rounded border-2 border-gray-600 bg-gray-700 text-gray-100 text-xl font-bold">
                        </td>
                        <td class="px-4 py-3 text-right text-lg text-gray-100">R$ ${item.preco.toFixed(2)}</td>
                        <td class="px-4 py-3 text-right text-lg font-bold text-gray-100">R$ ${totalItem.toFixed(2)}</td>
                        <td class="px-4 py-3 text-center">
                            <button onclick="removerItem(${idx})" class="text-red-500 hover:text-red-400 text-2xl font-bold">X</button>
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
            
            document.getElementById('total-venda').textContent = `R$ ${total.toFixed(2)}`;
        }

        document.getElementById('desconto-valor').addEventListener('input', () => {
            const subtotal = carrinho.reduce((acc, item) => acc + (item.preco * item.qtd), 0);
            calcularTotal(subtotal);
        });

        document.getElementById('desconto-tipo').addEventListener('change', () => {
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
            if (confirm('Cancelar venda atual?')) {
                carrinho = [];
                document.getElementById('cliente-id').value = '';
                document.getElementById('cliente-selecionado').classList.add('hidden');
                document.getElementById('desconto-valor').value = '';
                renderCarrinho();
                document.getElementById('busca-produto').focus();
            }
        }

        document.getElementById('busca-cliente').addEventListener('input', async (e) => {
            const termo = e.target.value;
            if (termo.length < 2) {
                document.getElementById('resultado-clientes').classList.add('hidden');
                return;
            }
            
            const res = await fetch(`{{ route('pdv.buscar-cliente') }}?q=${termo}`);
            const clientes = await res.json();
            
            const div = document.getElementById('resultado-clientes');
            div.innerHTML = '';
            
            clientes.forEach(c => {
                const item = document.createElement('div');
                item.className = 'px-4 py-3 hover:bg-gray-600 cursor-pointer text-lg text-gray-100 border-b border-gray-600';
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
            document.getElementById('cliente-selecionado').textContent = `Cliente: ${cliente.name}`;
            document.getElementById('cliente-selecionado').classList.remove('hidden');
        }

        async function finalizarVenda() {
            if (carrinho.length === 0) {
                alert('Carrinho vazio');
                return;
            }

            const clienteId = document.getElementById('cliente-id').value || null;
            const descontoValor = parseFloat(document.getElementById('desconto-valor').value) || 0;
            const descontoTipo = document.getElementById('desconto-tipo').value;
            const formaPagamento = document.getElementById('forma-pagamento').value;

            const dadosVenda = {
                carrinho: carrinho,
                cliente_id: clienteId,
                desconto: descontoValor,
                desconto_tipo: descontoTipo,
                forma_pagamento: formaPagamento
            };

            const btn = document.getElementById('btn-finalizar');
            const btnCancelar = document.getElementById('btn-cancelar');
            btn.disabled = true;
            btnCancelar.disabled = true;
            btn.textContent = 'PROCESSANDO...';
            processando = true;

            try {
                const res = await fetch('/pdv/finalizar-venda', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(dadosVenda)
                });

                const data = await res.json();

                if (data.success) {
                    alert(`Venda #${data.invoice_id} finalizada! Status: ${data.status}`);
                    if(data.erro_validacao){
                        console.log('Erro ao validar:', data.erro_validacao);
                        alert('Atenção: Fatura ficou em Rascunho. Verifica permissões no Dolibarr.');
                    }
                    carrinho = [];
                    document.getElementById('cliente-id').value = '';
                    document.getElementById('cliente-selecionado').classList.add('hidden');
                    document.getElementById('desconto-valor').value = '';
                    renderCarrinho();
                } else {
                    console.error('Erro Dolibarr:', data);
                    alert('Erro: ' + (data.dolibarr_response?.error?.message || data.error));
                }
            } catch (e) {
                console.error(e);
                alert('Erro de rede: ' + e.message);
            } finally {
                btn.disabled = false;
                btnCancelar.disabled = false;
                btn.textContent = 'F12 - FINALIZAR';
                processando = false;
            }
        }
    </script>
</x-app-layout>