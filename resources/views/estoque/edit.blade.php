<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Ajustar Estoque: {{ $dolibarr['label'] ?? $produto->ref_loja }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $produto->ref_loja ?? '---' }} | Dolibarr ID: {{ $produto->id_dolibarr }}</p>
            </div>
            <a href="{{ route('estoque.index') }}" class="bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar ao Estoque
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('estoque.update', $produto) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estoque atual</label>
                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $produto->stock_quantity) }}"
                                   min="0" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm">
                            <p class="text-xs text-gray-500 mt-1">Informe a quantidade de itens disponíveis. Deixe em branco para desativar o controle local.</p>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('estoque.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 mr-4">Cancelar</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Salvar Estoque
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
