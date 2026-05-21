<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Editar Produto: {{ $produto['label'] }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('produtos.update', $produto['id']) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nome -->
                        <div class="mb-4">
                            <label for="label" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome do Produto</label>
                            <input type="text" name="label" id="label" value="{{ old('label', $produto['label']) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm" required>
                        </div>

                        <!-- SKU - antigo ref_loja -->
                        <div class="mb-4">
                            <label for="ref_loja" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                            <input type="text" name="ref_loja" id="ref_loja" value="{{ old('ref_loja', $tenantVinculado->ref_loja) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Código exclusivo do produto na sua loja</p>
                        </div>

                        <!-- Preço -->
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preço</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $produto['price']) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estoque</label>
                            <input type="number" step="1" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $tenantVinculado->stock_quantity) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Atualize a quantidade disponível no estoque local. Deixe em branco para não usar controle de estoque.</p>
                        </div>

                        <!-- Código de Barras -->
                        <div class="mb-4">
                            <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código de Barras</label>
                            <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $produto['barcode']) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm">
                        </div>

                        <!-- Loja / Tenant - Só aparece se for admin -->
                        @if(auth()->user()->role === 'admin')
                        <div class="mb-4">
                            <label for="tenant_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Loja</label>
                            <select name="tenant_id" id="tenant_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm">
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ $tenantVinculado->tenant_id == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('produtos.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>