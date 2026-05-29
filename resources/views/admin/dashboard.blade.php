<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Painel Admin</h2>
                <p class="text-sm text-gray-600">Acesso somente para administradores superadmin.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.tenants.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Lojas</a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-900">Usuários</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold">Lojas</h3>
                    <p class="text-3xl font-bold mt-4">{{ $tenantCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold">Usuários</h3>
                    <p class="text-3xl font-bold mt-4">{{ $userCount }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold">Planos</h3>
                    <div class="mt-4 space-y-2">
                        <p>Free: {{ $plans['free'] ?? 0 }}</p>
                        <p>Pro: {{ $plans['pro'] ?? 0 }}</p>
                        <p>Enterprise: {{ $plans['enterprise'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
