<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Novo Produto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('produtos.store') }}">
                        @csrf

                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="mb-4">
                            <label for="ref_loja" class="block text-sm font-medium text-gray-700">SKU</label>
                            <input type="text" name="ref_loja" id="ref_loja" value="{{ old('ref_loja') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Código interno da sua loja. Ex: CAMISA-AZUL</p>
                        </div>

                        <div class="mb-4">
                            <label for="barcode" class="block text-sm font-medium text-gray-700">Código de Barras</label>
                            <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" 
                                   placeholder="7891000100103">
                            <p class="text-xs text-gray-500 mt-1">Opcional. EAN-13, EAN-8, UPC, etc.</p>
                        </div>

                        <div class="mb-4">
                            <label for="label" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <input type="text" name="label" id="label" value="{{ old('label') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Preço</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700">Estoque inicial</label>
                            <input type="number" step="1" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity') }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Defina a quantidade disponível no estoque local. Deixe em branco para não usar controle de estoque.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('produtos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Criar Produto
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>