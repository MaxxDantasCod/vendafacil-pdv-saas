<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Produto: {{ $produto['label'] }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <form method="POST" action="{{ route('produtos.update', $produto['id']) }}">
                        @csrf
                        @method('PUT')

                        <!-- Nome -->
                        <div class="mb-4">
                            <label for="label" class="block text-sm font-medium text-gray-700">Nome do Produto</label>
                            <input type="text" name="label" id="label" value="{{ old('label', $produto['label']) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <!-- Ref -->
                        <div class="mb-4">
                            <label for="ref" class="block text-sm font-medium text-gray-700">Referência</label>
                            <input type="text" name="ref" id="ref" value="{{ old('ref', $tenantVinculado->ref_loja) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <!-- Preço -->
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Preço</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $produto['price']) }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>

                        <!-- Loja / Tenant - Só aparece se for admin -->
                        @if(auth()->user()->role === 'admin')
                        <div class="mb-4">
                            <label for="tenant_id" class="block text-sm font-medium text-gray-700">Loja</label>
                            <select name="tenant_id" id="tenant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ $tenantVinculado->tenant_id == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('produtos.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Cancelar</a>
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