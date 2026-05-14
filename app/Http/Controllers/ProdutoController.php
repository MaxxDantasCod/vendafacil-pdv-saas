<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProdutoController extends Controller
{
    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ref' => 'required|string|max:128',
            'nome' => 'required|string|max:255',
            'preco' => 'required|numeric|min:0',
            'codigo_barras' => 'nullable|string',
            
        ]);
    
        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->post(env('DOLIBARR_BASE_URL') . '/api/index.php/products', [
            'ref' => $request->ref,
            'label' => $request->nome,
            'price' => $request->preco,
            'barcode' => $request->codigo_barras,
            'status' => 1, // Pra venda
            'status_buy' => 0, // Não compra
            'type' => 0 // 0 = Produto, 1 = Serviço
        ]);
    
        if ($response->successful()) {
            return redirect()->route('produtos.create')->with('success', 'Produto criado com sucesso!');
        }
    
        // Mostra o erro real do Dolibarr
        $erro = $response->json();
        return back()->with('error', 'Erro Dolibarr: ' . json_encode($erro));
    }
    // Editado Maxsuell Dantas
public function index(Request $request)
{
    $termo = $request->input('busca');
    
    $url = env('DOLIBARR_BASE_URL') . '/api/index.php/products?sortfield=t.rowid&sortorder=DESC&limit=50';
    
    if ($termo) {
        $url .= '&sqlfilters=(t.label:like:\'%'.$termo.'%\') or (t.ref:like:\'%'.$termo.'%\')';
    }

    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->get($url);

    $produtos = [];
    if ($response->successful()) {
        $produtos = $response->json();
    }

    return view('produtos.index', compact('produtos', 'termo'));
}
   public function edit(Product $product)
{
    $this->authorize('update', $product); // 403 se não puder
    return view('products.edit', compact('product'));
}

public function update(Request $request, Product $product)
{
    $this->authorize('update', $product);
    $product->update($request->validated());
    return redirect()->route('products.index');
}

public function destroy(Product $product)
{
    $this->authorize('delete', $product);
    $product->delete();
    return redirect()->route('products.index');
}
}