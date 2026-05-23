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

        // 1. Pega SÓ os vínculos dessa loja
        $vinculos = Produto::where('tenant_id', $tenantId)->get();
        $produtos = [];

        // 2. Busca um por um no Dolibarr pra não quebrar o filtro
        foreach ($vinculos as $vinculo) {
            $response = Http::withHeaders([
                'DOLAPIKEY' => env('DOLIBARR_API_KEY')
            ])->get(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$vinculo->id_dolibarr}");

            if ($response->successful()) {
                $produtoDoli = $response->json();
                $produtoDoli['ref_loja'] = $vinculo->ref_loja;
                $produtoDoli['stock_quantity'] = $vinculo->stock_quantity;
                $produtoDoli['produto_local_id'] = $vinculo->id;
                $produtos[] = $produtoDoli;
            }
        }

        return view('produtos.index', [
            'produtos' => $produtos
        ]);
    }

    public function edit($id)
    {
        $tenantId = auth()->user()->tenant_id;

        $tenantVinculado = Produto::where('id_dolibarr', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->get(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$id}");

        if ($response->failed()) {
            abort(404, 'Produto não encontrado no Dolibarr');
        }

        $produto = $response->json();

        $tenants = [];
        if (auth()->user()->role === 'admin') {
            $tenants = Tenant::all();
        }

        return view('produtos.edit', compact('produto', 'tenantVinculado', 'tenants'));
    }

    public function update(Request $request, $id)
    {
        $tenantId = auth()->user()->tenant_id;

        $vinculo = Produto::where('id_dolibarr', $id)
            ->where('tenant_id', $tenantId)
            ->firstOrFail();

        $request->validate([
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        // Valida SKU duplicado na loja
        if ($request->ref_loja && $request->ref_loja != $vinculo->ref_loja) {
            $existeNaLoja = Produto::where('tenant_id', $tenantId)
                ->where('ref_loja', $request->ref_loja)
                ->where('id', '!=', $vinculo->id)
                ->exists();

            if ($existeNaLoja) {
                return back()->withInput()->with('error', 'Já existe um produto com esse SKU na sua loja.');
            }
        }

        // Atualiza no Dolibarr - SEM O REF
        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->put(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$id}", [
            'label' => $request->label,
            'price' => $request->price,
            'barcode' => $request->barcode,
        ]);

        if ($response->failed()) {
            Log::error('DOLIBARR UPDATE ERROR', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            return back()->with('error', 'Erro ao atualizar no Dolibarr: ' . $response->body());
        }

        // Atualiza SKU local e estoque
        $vinculo->update([
            'ref_loja' => $request->ref_loja,
            'stock_quantity' => $request->filled('stock_quantity') ? $request->stock_quantity : null,
        ]);

        if (auth()->user()->role === 'admin' && $request->has('tenant_id')) {
            $vinculo->update(['tenant_id' => $request->tenant_id]);
        }

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado!');
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:255',
            'ref_loja' => 'nullable|string|max:128',
            'price' => 'required|numeric|min:0',
            'barcode' => 'nullable|string|max:255',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $tenantId = auth()->user()->tenant_id;

        if ($request->ref_loja) {
            $existeNaLoja = Produto::where('tenant_id', $tenantId)
                ->where('ref_loja', $request->ref_loja)
                ->exists();

            if ($existeNaLoja) {
                return back()->withInput()->with('error', 'Já existe um produto com essa SKU na sua loja.');
            }
        }

        $refDolibarr = $request->ref_loja ?: 'LOJA' . $tenantId . '-' . time();

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->post(env('DOLIBARR_BASE_URL') . '/api/index.php/products', [
            'ref' => $refDolibarr,
            'label' => $request->label,
            'price' => $request->price,
            'type' => 0,
            'barcode' => $request->barcode,
            'status' => 1,
            'status_buy' => 1,
        ]);

        if ($response->failed()) {
            Log::error('DOLIBARR CREATE ERROR', [
                'status' => $response->status(),
                'body' => $response->json(),
                'request' => $request->all()
            ]);
            
            $erro = $response->json();
            $msg = $erro['error']['message'] ?? $response->body();
            return back()->withInput()->with('error', 'Dolibarr: ' . $msg);
        }

        $idDolibarr = $response->json();

        Produto::create([
            'id_dolibarr' => $idDolibarr,
            'tenant_id'   => $tenantId,
            'ref_loja'    => $request->ref_loja,
            'stock_quantity' => $request->filled('stock_quantity') ? $request->stock_quantity : null,
        ]);

        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }

    public function destroy($id)
    {
        $tenantId = auth()->user()->tenant_id;

        $vinculo = Produto::where('id_dolibarr', $id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$vinculo) {
            return redirect()->route('produtos.index')->with('error', 'Produto não encontrado ou não pertence à sua loja.');
        }

        try {
            $vinculo->delete();
            return redirect()->route('produtos.index')->with('success', 'Vínculo do produto removido com sucesso.');
        } catch (\Exception $e) {
            Log::error('Erro ao deletar produto vinculado', ['id_dolibarr' => $id, 'tenant' => $tenantId, 'error' => $e->getMessage()]);
            return redirect()->route('produtos.index')->with('error', 'Erro ao deletar produto: ' . $e->getMessage());
        }
    }
}