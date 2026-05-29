<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detalhes da Loja</h2>
                <p class="text-sm text-gray-600">Todas as informações da loja, status do plano e usuários vinculados.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">Voltar</a>
                <a href="{{ route('admin.tenants.edit', $tenant) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Editar Loja</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold">Dados da loja</h3>
                        <dl class="mt-4 space-y-3 text-sm text-gray-700">
                            <div>
                                <dt class="font-medium">Nome</dt>
                                <dd>{{ $tenant->name }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">Email</dt>
                                <dd>{{ $tenant->email }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">URL Dolibarr</dt>
                                <dd>{{ $tenant->dolibarr_url }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">Database Dolibarr</dt>
                                <dd>{{ $tenant->dolibarr_db }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">Plano</dt>
                                <dd>{{ ucfirst($tenant->plan) }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">Status</dt>
                                <dd>{{ $tenant->status_pt }}</dd>
                            </div>
                        </dl>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Informações adicionais</h3>
                        <dl class="mt-4 space-y-3 text-sm text-gray-700">
                            <div>
                                <dt class="font-medium">Criado em</dt>
                                <dd>{{ $tenant->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">Última atualização</dt>
                                <dd>{{ $tenant->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium">Usuários vinculados</dt>
                                <dd>{{ $tenant->users->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Usuários da loja</h3>
                @if($tenant->users->isEmpty())
                    <p class="text-sm text-gray-600">Nenhum usuário vinculado à esta loja.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tenant->users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($user->role) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
