<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Editar Produto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('produtos.update', $produto['id']) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium">Ref.</label>
                            <input type="text" value="{{ $produto['ref'] }}" disabled 
                                   class="mt-1 block w-full rounded-md bg-gray-100">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium">Nome</label>
                            <input type="text" name="nome" value="{{ $produto['label'] }}" 
                                   class="mt-1 block w-full rounded-md" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium">Preço</label>
                            <input type="number" step="0.01" name="preco" value="{{ $produto['price'] }}" 
                                   class="mt-1 block w-full rounded-md" required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium">Código de Barras</label>
                            <input type="text" name="codigo_barras" value="{{ $produto['barcode'] }}" 
                                   class="mt-1 block w-full rounded-md">
                        </div>

                        <button type="submit" 
                                class="rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white">
                            Salvar Alterações
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>