<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Cliente;

class ClienteController extends Controller
{
    public function index(Request $request)
{
    $termo = $request->input('busca');
    
    // Pega só os IDs do Dolibarr que pertencem ao tenant logado
    $idsPermitidos = Cliente::pluck('id_dolibarr')->toArray();
    
    if (empty($idsPermitidos)) {
        return view('clientes.index', ['clientes' => [], 'termo' => $termo]);
    }

    $url = env('DOLIBARR_BASE_URL') . '/api/index.php/thirdparties?type=1&sortfield=t.rowid&sortorder=DESC&limit=50';
    
    // Filtra no Dolibarr só pelos IDs que esse tenant pode ver
    $url .= '&sqlfilters=(t.rowid:in:'.implode(',', $idsPermitidos).')';
    
    if ($termo) {
        $url .= ' and ((t.nom:like:\'%'.$termo.'%\') or (t.email:like:\'%'.$termo.'%\'))';
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
        $idDolibarr = $response->json(); // Dolibarr retorna só o ID
        
        // Salva vínculo local com tenant_id
        Cliente::create([
            'id_dolibarr' => $idDolibarr,
            'tenant_id' => auth()->user()->tenant_id
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente criado com sucesso!');
    }

    return back()->withErrors('Erro ao criar cliente no Dolibarr');
}

    public function edit($id)
{
    // Verifica se esse cliente pertence ao tenant logado
    $clienteLocal = Cliente::where('id_dolibarr', $id)->first();
    
    if (!$clienteLocal) {
        abort(403, 'Você não tem permissão para editar este cliente');
    }

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
    // Verifica se esse cliente pertence ao tenant logado
    $clienteLocal = Cliente::where('id_dolibarr', $id)->first();
    
    if (!$clienteLocal) {
        abort(403, 'Você não tem permissão para atualizar este cliente');
    }

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
    // Verifica se esse cliente pertence ao tenant logado
    $clienteLocal = Cliente::where('id_dolibarr', $id)->first();
    
    if (!$clienteLocal) {
        abort(403, 'Você não tem permissão para deletar este cliente');
    }

    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->delete(env('DOLIBARR_BASE_URL') . "/api/index.php/thirdparties/{$id}");

    if ($response->successful()) {
        // Remove o vínculo local também
        $clienteLocal->delete();
        
        return redirect()->route('clientes.index')->with('success', 'Cliente deletado!');
    }

    return back()->withErrors('Erro ao deletar cliente');
}
}