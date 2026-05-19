<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PdvController extends Controller
{
    public function index()
    {
        return view('pdv.index');
    }

    public function buscarProduto(Request $request)
{
    $termo = $request->input('termo');
    $user = auth()->user();
    
    // Lei 2: Superadmin vê tudo
    // Lei 1: Lojista só vê da warehouse dele
    $warehouseId = $user->is_superadmin ? null : $user->tenant->dolibarr_warehouse_id;
    
    if (!$user->is_superadmin && !$warehouseId) {
        return response()->json(['error' => 'Loja sem armazém configurado no Dolibarr'], 403);
    }

    $url = env('DOLIBARR_BASE_URL'). '/api/index.php/products';
    
    // Filtro base: busca por ref ou label
    $sqlFilter = "(t.ref:like:'%".$termo."%') or (t.label:like:'%".$termo."%')";
    
    // Adiciona filtro de armazém se não for superadmin
    if ($warehouseId) {
        $sqlFilter = "(" . $sqlFilter . ") and (t.fk_warehouse:=:'" . $warehouseId . "')";
    }

    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->get($url, [
        'sqlfilters' => $sqlFilter,
        'limit' => 10
    ]);

    if ($response->successful()) {
        return response()->json($response->json());
    }

    return response()->json(['error' => 'Erro ao buscar produtos'], 500);
}

    public function buscarCliente(Request $request)
{
    // DEBUG - APAGA DEPOIS QUE TESTAR
    dd([
        'user_id' => auth()->id(),
        'is_superadmin' => auth()->user()->is_superadmin,
        'tenant_id' => auth()->user()->tenant_id,
        'dolibarr_tenant_id' => optional(auth()->user()->tenant)->dolibarr_tenant_id,
        'termo' => $request->input('termo')
    ]);

    $termo = $request->input('termo');
    $user = auth()->user();
    
    $lojaId = $user->is_superadmin ? null : optional($user->tenant)->dolibarr_tenant_id;
    
    if (!$user->is_superadmin && !$lojaId) {
        return response()->json(['error' => 'Loja sem vínculo de clientes no Dolibarr'], 403);
    }

    $url = env('DOLIBARR_BASE_URL'). '/api/index.php/thirdparties';
    
    $sqlFilter = "(t.nom:like:'%".$termo."%') or (t.name_alias:like:'%".$termo."%')";
    
    if ($lojaId) {
        $sqlFilter = "(" . $sqlFilter . ") and (ef.loja_id:=:'" . $lojaId . "')";
    }

    $response = Http::withHeaders([
        'DOLAPIKEY' => env('DOLIBARR_API_KEY')
    ])->get($url, [
        'sqlfilters' => $sqlFilter,
        'limit' => 10
    ]);

    if ($response->successful()) {
        return response()->json($response->json());
    }

    return response()->json(['error' => 'Erro ao buscar clientes'], 500);
}

public function finalizarVenda(Request $request)
{
    try {
        $carrinho = $request->input('carrinho');
        $clienteId = $request->input('cliente_id');
        $desconto = $request->input('desconto', 0);
        $descontoTipo = $request->input('desconto_tipo', 'valor');
        $formaPagamento = $request->input('forma_pagamento', 'dinheiro');

        if (empty($carrinho)) {
            return response()->json(['error' => 'Carrinho vazio'], 400);
        }

        if (empty($clienteId)) {
            $urlCliente = env('DOLIBARR_BASE_URL'). '/api/index.php/thirdparties';
            $resCliente = Http::withHeaders([
                'DOLAPIKEY' => env('DOLIBARR_API_KEY')
            ])->get($urlCliente, [
                'sqlfilters' => '((t.client:=:1) or (t.client:=:3))',
                'limit' => 1
            ]);

            if (!$resCliente->successful() || empty($resCliente->json())) {
                return response()->json(['error' => 'Nenhum cliente encontrado no Dolibarr'], 400);
            }
            $clienteId = $resCliente->json()[0]['id'];
        }

        // Calcula total bruto e % de desconto pra ratear
        $totalBruto = 0;
        foreach ($carrinho as $item) {
            $totalBruto += $item['preco'] * $item['qtd'];
        }

        $descontoPercentual = 0;
        if ($descontoTipo == 'percent') {
            $descontoPercentual = (float)$desconto;
        } else {
            $descontoPercentual = $totalBruto > 0? ($desconto / $totalBruto * 100) : 0;
        }

        // 1. CRIA A FATURA VAZIA
        $urlFatura = env('DOLIBARR_BASE_URL'). '/api/index.php/invoices';
        $dadosFatura = [
            'socid' => (int)$clienteId,
            'type' => 0,
            'date' => date('Y-m-d'),
            'note_public' => 'Venda PDV - '. strtoupper($formaPagamento)
        ];

        $responseFatura = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->post($urlFatura, $dadosFatura);

        $invoiceId = (int)$responseFatura->body();

        if (!$responseFatura->successful() || $invoiceId == 0) {
            return response()->json([
                'error' => 'Dolibarr recusou criar a fatura',
                'dolibarr_body' => $responseFatura->body()
            ], 500);
        }

        // 2. ADICIONA AS LINHAS JÁ COM DESCONTO
        $rang = 1;
        foreach ($carrinho as $item) {
            $urlLinha = env('DOLIBARR_BASE_URL'). '/api/index.php/invoices/'. $invoiceId. '/lines';

            $dadosLinha = [
                'desc' => $item['nome'],
                'subprice' => (float)$item['preco'],
                'qty' => (float)$item['qtd'],
                'tva_tx' => 0.0,
                'remise_percent' => round($descontoPercentual, 2), // DESCONTO AQUI
                'product_type' => 0,
                'rang' => $rang
            ];

            $resLinha = Http::withHeaders([
                'DOLAPIKEY' => env('DOLIBARR_API_KEY')
            ])->post($urlLinha, $dadosLinha);

            if (!$resLinha->successful()) {
                return response()->json([
                    'error' => 'Erro ao adicionar linha',
                    'dolibarr_response' => $resLinha->json()
                ], 500);
            }
            $rang++;
        }

        // 3. VALIDA A FATURA
        $urlValida = env('DOLIBARR_BASE_URL'). '/api/index.php/invoices/'. $invoiceId. '/validate';
        $responseValida = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->post($urlValida, ['notrigger' => 0]);

        $statusFatura = 'Rascunho';
        if ($responseValida->successful()) {
            $statusFatura = 'Validada';
        }

        return response()->json([
            'success' => true,
            'invoice_id' => $invoiceId,
            'status' => $statusFatura,
            'desconto_aplicado' => round($descontoPercentual, 2). '%',
            'total_bruto' => $totalBruto,
            'message' => 'Venda finalizada'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Exceção: '. $e->getMessage()
        ], 500);
    }
}
}