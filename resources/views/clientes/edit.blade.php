<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Cliente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('clientes.update', $cliente['id']) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="nome" class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" name="nome" id="nome" value="{{ $cliente['name'] }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" value="{{ $cliente['email'] ?? '' }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="mb-4">
                            <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                            <input type="text" name="telefone" id="telefone" value="{{ $cliente['phone'] ?? '' }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white">
                                Atualizar
                            </button>
                            <a href="{{ route('clientes.index') }}" class="text-sm text-gray-600">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>