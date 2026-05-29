<x-app-layout>
<div class="py-8 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-white mb-6">Minhas Faturas</h1>
    
    <div class="bg-gray-800 rounded-xl p-6 mb-6">
        <p class="text-gray-400">Plano atual: <span class="text-white font-bold uppercase">{{ $tenant->plan }}</span></p>
        <p class="text-gray-400">Próximo vencimento: <span class="text-white">{{ $tenant->next_due_date ? \Carbon\Carbon::parse($tenant->next_due_date)->format('d/m/Y') : '-' }}</span></p>
        <p class="text-gray-400">Status: <span class="text-{{ $tenant->plan_status=='active'?'green':'yellow' }}-400">{{ $tenant->plan_status }}</span></p>
    </div>

    <table class="w-full bg-gray-800 rounded-xl overflow-hidden">
        <thead class="bg-gray-900">
            <tr class="text-left text-gray-400 text-sm">
                <th class="p-4">Data</th><th>Valor</th><th>Status</th><th>Ação</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-700">
            @forelse($faturas as $f)
            <tr class="text-white">
                <td class="p-4">{{ \Carbon\Carbon::parse($f['dueDate'])->format('d/m/Y') }}</td>
                <td>R$ {{ number_format($f['value'],2,',','.') }}</td>
                <td>{{ $f['status'] }}</td>
                <td><a href="{{ $f['invoiceUrl'] }}" target="_blank" class="text-blue-400">Ver boleto</a></td>
            </tr>
            @empty
            <tr><td colspan="4" class="p-8 text-center text-gray-500">Nenhuma fatura ainda</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</x-app-layout>