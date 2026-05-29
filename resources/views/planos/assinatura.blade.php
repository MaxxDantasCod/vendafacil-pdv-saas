<x-app-layout>
<div class="py-8 max-w-4xl mx-auto px-4">
    <h1 class="text-3xl font-bold text-white mb-2">Minha Assinatura</h1>
    <p class="text-gray-400 mb-8">Gerencie seu plano e pagamentos</p>

    @if(session('success'))
        <div class="bg-green-900/30 border border-green-500 text-green-300 p-4 rounded-xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- CARD PRINCIPAL -->
    <div class="bg-[#1e293b] rounded-2xl p-8 border border-gray-700 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="text-2xl font-bold text-white">Plano {{ ucfirst($tenant->plan) }}</h2>
                <p class="text-gray-400 mt-1">
                    @if($tenant->plan == 'pro') R$ 59/mês
                    @elseif($tenant->plan == 'enterprise') R$ 149/mês
                    @else Gratuito @endif
                </p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-medium
                @if($tenant->plan_status == 'active') bg-green-900/50 text-green-400 border-green-700
                @elseif($tenant->plan_status == 'pending') bg-yellow-900/50 text-yellow-400 border border-yellow-700
                @else bg-red-900/50 text-red-400 border border-red-700 @endif">
                {{ ucfirst($tenant->plan_status) }}
            </span>
        </div>

        <div class="grid md:grid-cols-3 gap-6 py-6 border-t border-gray-700">
            <div>
                <p class="text-gray-500 text-sm">Próxima cobrança</p>
                <p class="text-white font-semibold text-lg">
                    {{ $tenant->next_due_date ? \Carbon\Carbon::parse($tenant->next_due_date)->format('d/m/Y') : '-' }}
                </p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Forma de pagamento</p>
                <p class="text-white font-semibold">Pix / Cartão</p>
            </div>
            <div>
                <p class="text-gray-500 text-sm">Renovação</p>
                <p class="text-white font-semibold">Automática</p>
            </div>
        </div>

        <div class="flex gap-3 mt-6">
            <a href="{{ route('planos.index') }}" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition">
                Alterar plano
            </a>
            <a href="{{ route('planos.faturas') }}" class="px-6 py-2.5 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-medium transition">
                Ver faturas
            </a>
            @if($tenant->plan != 'free')
            <form method="POST" action="{{ route('planos.cancelar') }}" onsubmit="return confirm('Cancelar assinatura?')">
                @csrf
                <button type="submit" class="px-6 py-2.5 bg-red-900/30 hover:bg-red-900/50 text-red-400 border border-red-800 rounded-xl font-medium transition">
                    Cancelar
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- ÚLTIMAS FATURAS -->
    <div class="bg-[#1e293b] rounded-2xl p-8 border border-gray-700">
        <h3 class="text-white font-bold text-lg mb-4">Últimas cobranças</h3>
        <div class="space-y-3">
            @forelse($faturas as $f)
            <div class="flex justify-between items-center py-3 border-b border-gray-800 last:border-0">
                <div>
                    <p class="text-white">{{ \Carbon\Carbon::parse($f['dueDate'])->format('d/m/Y') }}</p>
                    <p class="text-gray-500 text-sm">R$ {{ number_format($f['value'],2,',','.') }}</p>
                </div>
                <a href="{{ $f['invoiceUrl'] }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm">Ver boleto →</a>
            </div>
            @empty
            <p class="text-gray-500 text-center py-8">Nenhuma cobrança ainda</p>
            @endforelse
        </div>
    </div>
</div>
</x-app-layout>