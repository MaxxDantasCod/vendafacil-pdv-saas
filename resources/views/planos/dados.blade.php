<x-app-layout>
<div class="py-12 max-w-md mx-auto px-4">
    <div class="bg-[#1e293b] rounded-2xl p-8 border border-gray-700">
        <h1 class="text-2xl font-bold text-white mb-2">Dados para faturamento</h1>
        @if($errors->any())
    <div class="bg-red-900/50 border border-red-700 text-red-200 px-4 py-3 rounded-xl mb-4">
        {{ $errors->first() }}
    </div>
@endif
        <p class="text-gray-400 mb-6">Precisamos do seu CPF/CNPJ apenas para emitir a nota fiscal via Asaas</p>

        <form method="POST" action="{{ route('planos.dados.salvar') }}">
            @csrf
            <input type="hidden" name="plan" value="{{ $plan }}">

            <label class="text-gray-300 text-sm">CPF ou CNPJ</label>
            <input type="text" name="cpf_cnpj" id="cpf" required
                   class="w-full mt-2 mb-4 bg-gray-800 border border-gray-600 text-white rounded-xl px-4 py-3 focus:border-blue-500 focus:outline-none"
                   placeholder="000.000.000-00">

            <p class="text-xs text-gray-500 mb-6">🔒 Seus dados são criptografados e usados apenas para fins fiscais (LGPD)</p>
            <p class="text-xs text-gray-500 mb-6"><a href="{{ route('privacidade') }}" class="text-blue-400 hover:text-blue-300">Saiba mais sobre Política de Privacidade e Proteção de Dados Pessoais</a></p>
            <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">
                Continuar para pagamento
            </button>
        </form>
    </div>
</div>

<script>
// máscara simples
document.getElementById('cpf').addEventListener('input', function(e){
    let v = e.target.value.replace(/\D/g,'');
    if(v.length <= 11) v = v.replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d)/,'$1.$2').replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    else v = v.replace(/^(\d{2})(\d)/,'$1.$2').replace(/^(\d{2})\.(\d{3})(\d)/,'$1.$2.$3').replace(/\.(\d{3})(\d)/,'.$1/$2').replace(/(\d{4})(\d)/,'$1-$2');
    e.target.value = v;
});
</script>
</x-app-layout>