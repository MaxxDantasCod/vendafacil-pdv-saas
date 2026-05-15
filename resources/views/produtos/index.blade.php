<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Produtos
            </h2>
            <a href="{{ route('produtos.create') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Novo Produto
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(!empty($produtos) && is_array($produtos) && count($produtos) > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ref</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Preço</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($produtos as $produto)
                                    <tr>
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $produto['id'] ?? '-' }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $produto['ref_loja'] ?? '-' }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">{{ $produto['label'] ?? '-' }}</td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-gray-100">R$ {{ number_format($produto['price'] ?? 0, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 flex gap-2">
                                            <a href="{{ route('produtos.edit', $produto['id']) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Editar</a>
                                            <form method="POST" action="{{ route('produtos.destroy', $produto['id']) }}" onsubmit="return confirm('Tem certeza?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Excluir</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4">Nenhum produto encontrado para esta loja.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>