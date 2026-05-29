<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nova Loja</h2>
                <p class="text-sm text-gray-600">Cadastre uma nova loja e configure o plano inicial.</p>
            </div>
            <div>
                <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">Voltar</a>
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

                    <form method="POST" action="{{ route('admin.tenants.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nome da loja</label>
                                <input id="name" name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>

                            <div>
                                <label for="dolibarr_url" class="block text-sm font-medium text-gray-700">URL do Dolibarr</label>
                                <input id="dolibarr_url" name="dolibarr_url" type="url" value="{{ old('dolibarr_url') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>

                            <div>
                                <label for="dolibarr_db" class="block text-sm font-medium text-gray-700">Base de dados Dolibarr</label>
                                <input id="dolibarr_db" name="dolibarr_db" value="{{ old('dolibarr_db') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                            </div>

                            <div>
                                <label for="plan" class="block text-sm font-medium text-gray-700">Plano</label>
                                <select id="plan" name="plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="free" {{ old('plan') === 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="pro" {{ old('plan') === 'pro' ? 'selected' : '' }}>Pro</option>
                                    <option value="enterprise" {{ old('plan') === 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                </select>
                            </div>

                            <div>
                                <label for="plan_status" class="block text-sm font-medium text-gray-700">Status do plano</label>
                                <select id="plan_status" name="plan_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                                    <option value="active" {{ old('plan_status', 'active') === 'active' ? 'selected' : '' }}>Ativo</option>
                                    <option value="pending" {{ old('plan_status') === 'pending' ? 'selected' : '' }}>Aguardando pagamento</option>
                                    <option value="overdue" {{ old('plan_status') === 'overdue' ? 'selected' : '' }}>Atrasado</option>
                                    <option value="cancelled" {{ old('plan_status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                </select>
                            </div>

                            <div>
                                <label for="api_key" class="block text-sm font-medium text-gray-700">API Key Dolibarr</label>
                                <input id="api_key" name="api_key" value="{{ old('api_key') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-3">
                            <a href="{{ route('admin.tenants.index') }}" class="text-gray-600 hover:text-gray-900">Cancelar</a>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Criar Loja</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
