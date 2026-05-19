<?php

namespace App\Http\Controllers;

use App\Models\Caixa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CaixaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $caixaAberto = Caixa::where('user_id', $user->id)
            ->where('status', 'aberto')
            ->first();

        return view('caixa.index', compact('caixaAberto'));
    }

    public function abrir(Request $request)
    {
        $request->validate(['valor_inicial' => 'required|numeric|min:0']);
        
        $user = auth()->user();
        
        // Garante que não tem 2 caixas abertos
        Caixa::where('user_id', $user->id)->where('status', 'aberto')
            ->update(['status' => 'fechado', 'fechado_em' => now()]);

        $caixa = Caixa::create([
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'valor_inicial' => $request->valor_inicial,
            'aberto_em' => now(),
            'status' => 'aberto'
        ]);

        return response()->json(['success' => true, 'caixa' => $caixa]);
    }

    public function fechar(Request $request)
    {
        $request->validate([
            'valor_final' => 'required|numeric|min:0',
            'obs' => 'nullable|string'
        ]);

        $user = auth()->user();
        $caixa = Caixa::where('user_id', $user->id)->where('status', 'aberto')->first();

        if (!$caixa) {
            return response()->json(['error' => 'Nenhum caixa aberto'], 400);
        }

        $caixa->update([
            'valor_final' => $request->valor_final,
            'fechado_em' => now(),
            'status' => 'fechado',
            'obs_fechamento' => $request->obs
        ]);

        return response()->json(['success' => true, 'caixa' => $caixa]);
    }

    public function relatorioDia()
    {
        $user = auth()->user();
        $caixa = Caixa::where('user_id', $user->id)
            ->whereDate('aberto_em', today())
            ->latest()
            ->first();

        return response()->json($caixa);
    }

    // BUSCA PRODUTO - Usa vínculo local igual ProdutoController
public function buscarProduto(Request $request)
{
    $termo = $request->input('q');
    $tenantId = auth()->user()->tenant_id;
    
    $idsPermitidos = \App\Models\Produto::where('tenant_id', $tenantId)
        ->pluck('id_dolibarr')
        ->toArray();
    
    if (empty($idsPermitidos)) {
        return response()->json([]);
    }

    $url = env('DOLIBARR_BASE_URL'). '/api/index.php/products';
    
    // 1. Busca todos os produtos vinculados dessa loja
    $response = Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
        ->get($url, [
            'sqlfilters' => 't.rowid:in:[' . implode(',', $idsPermitidos) . ']',
            'limit' => 100
        ]);

    if (!$response->successful()) {
        return response()->json([]);
    }

    $produtos = $response->json();

    // 2. Filtra por termo no PHP: ref, label OU barcode
    if ($termo) {
        $termo = mb_strtolower($termo);
        $produtos = array_filter($produtos, function($p) use ($termo) {
            return str_contains(mb_strtolower($p['ref'] ?? ''), $termo) 
                || str_contains(mb_strtolower($p['label'] ?? ''), $termo)
                || str_contains(mb_strtolower($p['barcode'] ?? ''), $termo);
        });
    }

    // 3. Adiciona SKU da loja
    $produtos = array_map(function($p) use ($tenantId) {
        $vinculo = \App\Models\Produto::where('id_dolibarr', $p['id'])
            ->where('tenant_id', $tenantId)
            ->first();
        $p['ref_loja'] = $vinculo ? $vinculo->ref_loja : '';
        return $p;
    }, $produtos);

    return response()->json(array_values($produtos));
}
// BUSCA CLIENTE - Usa vínculo local igual ClienteController  
public function buscarCliente(Request $request)
{
    $termo = $request->input('q');
    $tenantId = auth()->user()->tenant_id;
    
    $idsPermitidos = \App\Models\Cliente::where('tenant_id', $tenantId)
        ->pluck('id_dolibarr')
        ->toArray();
    
    if (empty($idsPermitidos)) {
        return response()->json([]);
    }

    $url = env('DOLIBARR_BASE_URL'). '/api/index.php/thirdparties';
    
    // 1. Busca todos os clientes vinculados dessa loja
    $response = Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
        ->get($url, [
            'sqlfilters' => 't.rowid:in:[' . implode(',', $idsPermitidos) . ']',
            'limit' => 100
        ]);

    if (!$response->successful()) {
        return response()->json([]);
    }

    $clientes = $response->json();

    // 2. Filtra por termo no PHP: nome, email ou telefone
    if ($termo) {
        $termo = mb_strtolower($termo);
        $clientes = array_filter($clientes, function($c) use ($termo) {
            return str_contains(mb_strtolower($c['name'] ?? ''), $termo) 
                || str_contains(mb_strtolower($c['email'] ?? ''), $termo)
                || str_contains(mb_strtolower($c['phone'] ?? ''), $termo);
        });
    }

    return response()->json(array_values($clientes));
}
    // FINALIZAR VENDA - Copiado do PdvController + atualiza caixa
    public function finalizarVenda(Request $request)
    {
        try {
            $user = auth()->user();
            $caixa = Caixa::where('user_id', $user->id)->where('status', 'aberto')->first();
            
            if (!$caixa) {
                return response()->json(['error' => 'Abra o caixa antes de vender'], 400);
            }

            $carrinho = $request->input('carrinho');
            $clienteId = $request->input('cliente_id');
            $desconto = $request->input('desconto', 0);
            $descontoTipo = $request->input('desconto_tipo', 'valor');
            $formaPagamento = $request->input('forma_pagamento', 'dinheiro');

            if (empty($carrinho)) {
                return response()->json(['error' => 'Carrinho vazio'], 400);
            }

            // Cliente padrão se não selecionou
            if (empty($clienteId)) {
                $urlCliente = env('DOLIBARR_BASE_URL'). '/api/index.php/thirdparties';
                $resCliente = Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
                    ->get($urlCliente, ['sqlfilters' => '((t.client:=:1) or (t.client:=:3))', 'limit' => 1]);

                if (!$resCliente->successful() || empty($resCliente->json())) {
                    return response()->json(['error' => 'Nenhum cliente encontrado no Dolibarr'], 400);
                }
                $clienteId = $resCliente->json()[0]['id'];
            }

            $totalBruto = 0;
            foreach ($carrinho as $item) {
                $totalBruto += $item['preco'] * $item['qtd'];
            }

            $descontoPercentual = $descontoTipo == 'percent' ? (float)$desconto : ($totalBruto > 0 ? ($desconto / $totalBruto * 100) : 0);

            // 1. CRIA FATURA
            $urlFatura = env('DOLIBARR_BASE_URL'). '/api/index.php/invoices';
            $responseFatura = Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
                ->post($urlFatura, [
                    'socid' => (int)$clienteId,
                    'type' => 0,
                    'date' => date('Y-m-d'),
                    'note_public' => 'Venda PDV - '. strtoupper($formaPagamento)
                ]);

            $invoiceId = (int)$responseFatura->body();
            if (!$responseFatura->successful() || $invoiceId == 0) {
                return response()->json(['error' => 'Erro ao criar fatura no Dolibarr'], 500);
            }

            // 2. ADICIONA LINHAS
            $rang = 1;
            foreach ($carrinho as $item) {
                Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
                    ->post(env('DOLIBARR_BASE_URL'). '/api/index.php/invoices/'. $invoiceId. '/lines', [
                        'desc' => $item['nome'],
                        'subprice' => (float)$item['preco'],
                        'qty' => (float)$item['qtd'],
                        'tva_tx' => 0.0,
                        'remise_percent' => round($descontoPercentual, 2),
                        'product_type' => 0,
                        'rang' => $rang
                    ]);
                $rang++;
            }

            // 3. VALIDA
            Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
                ->post(env('DOLIBARR_BASE_URL'). '/api/index.php/invoices/'. $invoiceId. '/validate', ['notrigger' => 0]);

            // 4. ATUALIZA CAIXA
            $totalFinal = $totalBruto - ($descontoTipo == 'valor' ? $desconto : ($totalBruto * $desconto / 100));
            $caixa->increment('total_vendas', $totalFinal);
            $caixa->increment('total_'. $formaPagamento, $totalFinal);

            return response()->json([
                'success' => true,
                'invoice_id' => $invoiceId,
                'total' => $totalFinal
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Exceção: '. $e->getMessage()], 500);
        }
    }
}