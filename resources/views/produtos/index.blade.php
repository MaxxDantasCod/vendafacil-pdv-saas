<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Produtos') }}
            </h2>
            <a href="{{ route('produtos.create') }}" 
               class="rounded-xl bg-brand px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600">
                + Novo Produto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
    <div class="mb-4 rounded-lg bg-green-100 p-4 text-sm text-green-700">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif
<div class="mb-4 flex justify-between">
    <form method="GET" action="{{ route('produtos.index') }}" class="flex gap-2">
        <input type="text" name="busca" value="{{ $termo ?? '' }}" 
               placeholder="Buscar por nome ou ref..."
               class="rounded-md border-gray-300 px-3 py-2">
        <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
            Buscar
        </button>
        @if($termo)
            <a href="{{ route('produtos.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-800">
                Limpar
            </a>
        @endif
    </form>
</div>
    @if($errors->any())
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif    
                <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Ref.</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Preço</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Estoque</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse ($produtos as $produto)
                                <tr>
                                    <td class="px-6 py-4">{{ $produto['ref'] ?? '-' }}</td>
                                    <td class="px-6 py-4">{{ $produto['label'] ?? '-' }}</td>
                                    <td class="px-6 py-4">R$ {{ number_format($produto['price'] ?? 0, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4">{{ $produto['stock_reel'] ?? 0 }}</td>
                                    <td class="px-6 py-4 flex gap-2">
    <a href="{{ route('produtos.edit', $produto['id']) }}" 
       class="text-blue-600 hover:text-blue-900">Editar</a>
    
    <form action="{{ route('produtos.destroy', $produto['id']) }}" method="POST" 
          onsubmit="return confirm('Tem certeza que quer deletar?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-600 hover:text-red-900">Deletar</button>
    </form>
</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center">Nenhum produto encontrado no Dolibarr.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>