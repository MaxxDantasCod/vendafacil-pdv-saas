<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class EstoqueController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;
        $produtos = [];

        foreach (Produto::where('tenant_id', $tenantId)->get() as $vinculo) {
            $response = Http::withHeaders([
                'DOLAPIKEY' => env('DOLIBARR_API_KEY')
            ])->get(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$vinculo->id_dolibarr}");

            if ($response->successful()) {
                $produto = $response->json();
            } else {
                $produto = [
                    'id' => $vinculo->id_dolibarr,
                    'ref' => $vinculo->ref_loja,
                    'label' => 'Produto Dolibarr indisponível',
                    'price' => 0,
                ];
            }

            $produto['ref_loja'] = $vinculo->ref_loja;
            $produto['stock_quantity'] = $vinculo->stock_quantity;
            $produto['produto_local_id'] = $vinculo->id;
            $produtos[] = $produto;
        }

        return view('estoque.index', compact('produtos'));
    }

    public function edit(Produto $produto)
    {
        abort_if($produto->tenant_id !== auth()->user()->tenant_id, 403);

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->get(env('DOLIBARR_BASE_URL') . "/api/index.php/products/{$produto->id_dolibarr}");

        $dolibarr = $response->successful() ? $response->json() : null;

        return view('estoque.edit', compact('produto', 'dolibarr'));
    }

    public function update(Request $request, Produto $produto)
    {
        abort_if($produto->tenant_id !== auth()->user()->tenant_id, 403);

        $request->validate([
            'stock_quantity' => 'nullable|integer|min:0',
        ]);

        $produto->update([
            'stock_quantity' => $request->filled('stock_quantity') ? $request->stock_quantity : null,
        ]);

        return redirect()->route('estoque.index')->with('success', 'Estoque atualizado com sucesso.');
    }
}
