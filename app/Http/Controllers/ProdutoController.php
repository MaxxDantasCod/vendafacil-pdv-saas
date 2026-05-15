<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProdutoController extends Controller
{

public function index()
{
    $tenantId = auth()->user()->tenant_id;

    // Pega vínculos locais
    $vinculos = Produto::where('tenant_id', $tenantId)->get();

    $idsDolibarr = $vinculos->pluck('id_dolibarr')->toArray();
    $produtosDolibarr = [];

    if (!empty($idsDolibarr)) {
        $sqlFilter = 't.rowid:in:[' . implode(',', $idsDolibarr) . ']';

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->get(env('DOLIBARR_BASE_URL') . '/api/index.php/products', [
            'sqlfilters' => $sqlFilter,
            'limit'      => 100
        ]);

        if ($response->successful() && is_array($response->json())) {
            $produtosDolibarr = $response->json();
        }
    }

    // Junta o ref_loja local com os dados do Dolibarr
    $produtos = [];
    foreach ($produtosDolibarr as $produtoDoli) {
        $vinculo = $vinculos->firstWhere('id_dolibarr', $produtoDoli['id']);
        $produtoDoli['ref_loja'] = $vinculo ? $vinculo->ref_loja : '-';
        $produtos[] = $produtoDoli;
    }

    return view('produtos.index', [
        'produtos' => $produtos
    ]);
}

public function edit($id)
{
    $tenantId = auth()->user()->tenant_id;

    // 1. Confere se esse produto do Dolibarr pertence à loja do usuário logado
    $tenantVinculado = Produto::where('id_dolibarr', $id)
        ->where('tenant_id', $tenantId)
        ->firstOrFail();

    // 2. Busca dados no Dolibarr
    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->get(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$id}");

    if ($response->failed()) {
        abort(404, 'Produto não encontrado no Dolibarr');
    }

    $produto = $response->json();

    // 3. Se for admin, carrega todas as lojas pro select
    $tenants = [];
    if (auth()->user()->role === 'admin') {
        $tenants = \App\Models\Tenant::all();
    }

    return view('produtos.edit', compact('produto', 'tenantVinculado', 'tenants'));
}

             public function update(Request $request, $id)
{
    $tenantId = auth()->user()->tenant_id;

    // 1. Confere vínculo atual
    $vinculo = Produto::where('id_dolibarr', $id)
        ->where('tenant_id', $tenantId)
        ->firstOrFail();

    // 2. Atualiza no Dolibarr
    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->put(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$id}", [
        'label' => $request->label,
        'price' => $request->price,
        'ref'   => $request->ref,
    ]);

    if ($response->failed()) {
        return back()->with('error', 'Erro ao atualizar no Dolibarr: ' . $response->body());
    }

    // 3. Se for admin e mudou a loja, atualiza o vínculo local
    if (auth()->user()->role === 'admin' && $request->has('tenant_id')) {
        $vinculo->update(['tenant_id' => $request->tenant_id]);
    }

    return redirect()->route('produtos.index')->with('success', 'Produto atualizado!');
}

public function destroy($id)
{
    $tenantId = auth()->user()->tenant_id;

    // 1. Confere vínculo e apaga local
    $vinculo = Produto::where('id_dolibarr', $id)
        ->where('tenant_id', $tenantId)
        ->firstOrFail();
    
    $vinculo->delete(); // Só apaga o vínculo da loja

    // 2. Deleta no Dolibarr - CUIDADO: isso apaga pra todas as lojas
    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->delete(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$id}");

    if ($response->failed()) {
        Log::error('Erro ao deletar no Dolibarr: ' . $response->body());
    }

    return redirect()->route('produtos.index')->with('success', 'Produto removido!');
}

public function create()
{
    return view('produtos.create');
}

public function store(Request $request)
{
    $request->validate([
        'label' => 'required|string|max:255',
        'ref_loja' => 'required|string|max:128', // Agora valida o REF da loja
        'price' => 'required|numeric|min:0',
    ]);

    $tenantId = auth()->user()->tenant_id;

    // 1. Valida se já existe esse REF na loja atual
    $existeNaLoja = Produto::where('tenant_id', $tenantId)
        ->where('ref_loja', $request->ref_loja)
        ->exists();

    if ($existeNaLoja) {
        return back()->withInput()->with('error', 'Já existe um produto com essa referência na sua loja.');
    }

    // 2. Gera REF único pro Dolibarr: LOJA-123-TIMESTAMP
    $refDolibarr = 'LOJA' . $tenantId . '-' . time();

    // 3. Tenta criar no Dolibarr
    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->post(env('DOLIBARR_BASE_URL') . '/api/index.php/products', [
        'label' => $request->label,
        'ref'   => $refDolibarr, // REF único pro Dolibarr
        'price' => $request->price,
        'status' => 1,
        'status_buy' => 1,
    ]);

    if ($response->failed()) {
        // Pega o erro real do Dolibarr
        $erro = $response->json();
        $msg = $erro['error']['message'] ?? 'Erro desconhecido ao criar no Dolibarr';
        return back()->withInput()->with('error', 'Dolibarr: ' . $msg);
    }

    // 4. Pega o ID que o Dolibarr retornou
    $idDolibarr = $response->json();

    // 5. Salva vínculo + REF da loja
    Produto::create([
        'id_dolibarr' => $idDolibarr,
        'tenant_id'   => $tenantId,
        'ref_loja'    => $request->ref_loja, // REF que o cliente digitou
    ]);

    return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
}
}