<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Usuário</h2>
                <p class="text-sm text-gray-600">Atualize o perfil e a loja vinculada do usuário.</p>
            </div>
            <div>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">Voltar</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                                <input id="name" name="name" value="{{ old('name', $user->name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700">Nova senha</label>
                                <input id="password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                <p class="text-xs text-gray-500 mt-1">Deixe em branco para manter a senha atual.</p>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmação de senha</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                            <div>
                                <label for="role" class="block text-sm font-medium text-gray-700">Perfil</label>
                                <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="superadmin" {{ old('role', $user->role) === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                    <option value="dono" {{ old('role', $user->role) === 'dono' ? 'selected' : '' }}>Dono</option>
                                    <option value="funcionario" {{ old('role', $user->role) === 'funcionario' ? 'selected' : '' }}>Funcionário</option>
                                </select>
                            </div>
                            <div>
                                <label for="tenant_id" class="block text-sm font-medium text-gray-700">Loja (opcional)</label>
                                <select id="tenant_id" name="tenant_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="" {{ old('tenant_id', $user->tenant_id) ? '' : 'selected' }}>Sem loja</option>
                                    @foreach($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id', $user->tenant_id) == $tenant->id ? 'selected' : '' }}>{{ $tenant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-3">
                            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
