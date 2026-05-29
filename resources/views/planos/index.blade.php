<x-app-layout>
    <div class="py-12 bg-gray-900 min-h-screen">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-white">Escolha seu plano</h1>
                <p class="text-gray-400 mt-2">Você usou <span class="text-white font-bold">{{ $usage }}/50</span> vendas este mês</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                
                @php
                    function tip($text) {
                        return '<span class="relative inline-block group ml-1 align-middle">
                            <svg class="w-4 h-4 text-gray-400 hover:text-yellow-400 cursor-help" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                            <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-56 p-2 text-xs text-white bg-gray-800 border border-gray-700 rounded-lg shadow-lg z-10">'.$text.'</span>
                        </span>';
                    }
                @endphp

                <!-- FREE -->
                <div class="bg-gray-800 border-2 {{ $currentPlan=='free' ? 'border-green-500' : 'border-gray-700' }} rounded-xl p-6">
                    <h3 class="text-xl font-bold text-white">Free</h3>
                    <p class="text-4xl font-bold text-white my-4">R$ 0</p>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li>✓ 50 vendas/mês {!! tip('Limite de 50 vendas por mês.') !!}</li>
                        <li>✓ 1 usuário {!! tip('Apenas o dono.') !!}</li>
                    </ul>
                    <button disabled class="mt-6 w-full bg-gray-600 py-3 rounded-lg">Seu plano</button>
                </div>

                <!-- BÁSICO -->
                <div class="bg-gray-800 border-2 border-blue-500 rounded-xl p-6 relative">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-blue-500 text-xs font-bold px-3 py-1 rounded-full">MAIS POPULAR</div>
                    <h3 class="text-xl font-bold text-white">Pro</h3>
                    <p class="text-4xl font-bold text-white my-4">R$ 59<span class="text-lg text-gray-400">/mês</span></p>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li>✓ Vendas ilimitadas {!! tip('Vendas sem limites durante todo o mês, sem medo de travar a operação.') !!}</li>
                        <li>✓ 3 usuários {!! tip('Cadastre até 3 funcionários.') !!}</li>
                        <li>✓ Suporte WhatsApp</li>
                    </ul>
                    <a href="{{ route('planos.upgrade', 'pro') }}" 
           class="block w-full py-3 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-xl font-semibold transition-all shadow-lg shadow-blue-900/30">
            Fazer Upgrade
        </a>
                </div>

                <!-- PRO -->
                <div class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                    <h3 class="text-xl font-bold text-white">Enterprise</h3>
                    <p class="text-4xl font-bold text-white my-4">R$ 149<span class="text-lg text-gray-400">/mês</span></p>
                    <ul class="space-y-3 text-gray-300 text-sm">
                        <li>✓ Tudo do Básico</li>
                        <li>✓ Usuários ilimitados {!! tip('Sem limite de funcionários.') !!}</li>
                    </ul>
                    <button disabled class="mt-6 w-full bg-gray-700 py-3 rounded-lg">Em breve</button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>