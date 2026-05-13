<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $termo = $request->input('busca');
        
        $url = env('DOLIBARR_BASE_URL') . '/api/index.php/thirdparties?type=1&sortfield=t.rowid&sortorder=DESC&limit=50';
        
        if ($termo) {
            $url .= '&sqlfilters=(t.nom:like:\'%'.$termo.'%\') or (t.email:like:\'%'.$termo.'%\')';
        }

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->get($url);

        $clientes = [];
        if ($response->successful()) {
            $clientes = $response->json();
        }

        return view('clientes.index', compact('clientes', 'termo'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string',
        ]);

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->post(env('DOLIBARR_BASE_URL') . '/api/index.php/thirdparties', [
            'name' => $request->nome,
            'email' => $request->email,
            'phone' => $request->telefone,
            'client' => 1,
            'status' => 1,
        ]);

        if ($response->successful()) {
            return redirect()->route('clientes.index')->with('success', 'Cliente criado com sucesso!');
        }

        return back()->withErrors('Erro ao criar cliente no Dolibarr');
    }

    public function edit($id)
    {
        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->get(env('DOLIBARR_BASE_URL') . "/api/index.php/thirdparties/{$id}");

        if ($response->failed()) {
            abort(404, 'Cliente não encontrado');
        }

        $cliente = $response->json();
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefone' => 'nullable|string',
        ]);

        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->put(env('DOLIBARR_BASE_URL') . "/api/index.php/thirdparties/{$id}", [
            'name' => $request->nome,
            'email' => $request->email,
            'phone' => $request->telefone,
        ]);

        if ($response->successful()) {
            return redirect()->route('clientes.index')->with('success', 'Cliente atualizado!');
        }

        return back()->withErrors('Erro ao atualizar cliente');
    }

    public function destroy($id)
    {
        $response = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->delete(env('DOLIBARR_BASE_URL') . "/api/index.php/thirdparties/{$id}");

        if ($response->successful()) {
            return redirect()->route('clientes.index')->with('success', 'Cliente deletado!');
        }

        return back()->withErrors('Erro ao deletar cliente');
    }
}