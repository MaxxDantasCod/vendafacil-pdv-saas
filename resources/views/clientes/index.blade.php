<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Clientes') }}
            </h2>
            <a href="{{ route('clientes.create') }}" class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                Novo Cliente
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
                        <form method="GET" action="{{ route('clientes.index') }}" class="flex gap-2">
                            <input type="text" name="busca" value="{{ $termo ?? '' }}" 
                                   placeholder="Buscar por nome ou email..."
                                   class="rounded-md border-gray-300 px-3 py-2">
                            <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
                                Buscar
                            </button>
                            @if($termo)
                                <a href="{{ route('clientes.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-800">
                                    Limpar
                                </a>
                            @endif
                        </form>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nome</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Telefone</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($clientes as $cliente)
                                <tr>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">{{ $cliente['name'] ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">{{ $cliente['email'] ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm">{{ $cliente['phone'] ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('clientes.edit', $cliente['id']) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                        <form action="{{ route('clientes.destroy', $cliente['id']) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ml-4 text-red-600 hover:text-red-900">Deletar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        Nenhum cliente encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>