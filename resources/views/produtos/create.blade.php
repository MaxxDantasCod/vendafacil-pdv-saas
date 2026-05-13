<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Cadastrar Produto
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('error'))
                        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('produtos.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <input type="text" name="nome" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Preço R$</label>
                            <input type="number" step="0.01" name="preco" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
  
                        <div class="mb-4">
    <label class="block text-sm font-medium text-gray-700">SKU / Ref</label>
    <input type="text" name="ref" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
    <p class="text-xs text-gray-500 mt-1">Código único do produto. Ex: CAM-001</p>
</div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700">Código de Barras</label>
                            <input type="text" name="codigo_barras" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                            Salvar Produto
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>