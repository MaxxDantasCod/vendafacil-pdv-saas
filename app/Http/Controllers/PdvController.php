<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Caixa;
use App\Models\Produto;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\CaixaMovimento;
use App\Models\SalesUsageMonthly;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdvController extends Controller
{
public function index()
{
    $user = auth()->user();
    $tenant = $user->tenant;
    $plan = $tenant?->plan ?? 'free';
    if (in_array($plan, ['basico','basic'])) $plan = 'free';

    $planUsage = 0;
    if ($plan === 'free') {
        $planUsage = SalesUsageMonthly::where('user_id', $user->id)
            ->where('year_month', now()->format('Y-m'))
            ->value('sales_count') ?? 0;
    }
    $planLimit = $plan === 'free' ? 50 : null;
    $planUsage = 48; // FORÇAR TESTE

    // a view do caixa precisa saber se tem caixa aberto
    $caixaAberto = Caixa::where('user_id', $user->id)
        ->where('status', 'aberto')
        ->exists();

    return view('caixa.index', [
        'caixaAberto' => $caixaAberto,
        'planUsage'   => $planUsage,
        'planLimit'   => $planLimit,
    ]);
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
        // aceita array de pagamentos: [{forma:'dinheiro', valor: 10}, ...]
        $pagamentos = $request->input('pagamentos');
        $formaPagamento = $request->input('forma_pagamento', 'dinheiro');

        if (empty($carrinho)) {
            return response()->json(['error' => 'Carrinho vazio'], 400);
        }

        // === CONTROLE PLANO FREE - NÃO QUEBRA SE DER ERRO ===
try {
    $user = auth()->user();
    $plan = $user->tenant->plan ?? 'free';
    
    if ($plan === 'free') {
        $usage = SalesUsageMonthly::firstOrCreate(
            ['user_id' => $user->id, 'year_month' => now()->format('Y-m')],
            ['sales_count' => 0]
        );
        
        if ($usage->sales_count >= 50) {
            return response()->json([
                'error' => 'Limite do plano Free atingido (50 vendas/mês). Faça upgrade para Pro.'
            ], 403);
        }
    }
} catch (\Exception $e) {
    // Se der qualquer erro aqui, deixa vender - não quebra o PDV
    \Log::warning('Erro verificação plano: '.$e->getMessage());
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

        // Persistência local + movimentação de caixa (se houver caixa aberto)
        try {
            $user = auth()->user();
            $caixa = Caixa::where('user_id', $user->id)->where('status', 'aberto')->first();

            DB::transaction(function () use ($carrinho, $clienteId, $descontoPercentual, $invoiceId, $user, $caixa, $pagamentos, $formaPagamento) {
                // Se tiver caixa aberto, registra invoice local e movimentos
                $totalBruto = 0;
                foreach ($carrinho as $item) {
                    $totalBruto += $item['preco'] * $item['qtd'];
                }
                $totalFinal = $descontoPercentual > 0 ? ($totalBruto - ($totalBruto * $descontoPercentual / 100)) : $totalBruto;

                if ($caixa) {
                    $invoice = Invoice::create([
                        'invoice_id_dolibarr' => $invoiceId,
                        'caixa_id' => $caixa->id,
                        'tenant_id' => $caixa->tenant_id,
                        'user_id' => $user->id,
                        'total' => $totalFinal,
                        'forma_pagamento' => $pagamentos ? 'multiplo' : $formaPagamento,
                        'invoice_date' => now(),
                    ]);

                    foreach ($carrinho as $item) {
                        InvoiceItem::create([
                            'invoice_id' => $invoice->id,
                            'product_id_dolibarr' => $item['id'] ?? null,
                            'nome' => $item['nome'] ?? '',
                            'ref_loja' => null,
                            'qtd' => (int)$item['qtd'],
                            'preco' => (float)$item['preco'],
                            'total' => (float)($item['preco'] * $item['qtd']),
                            'tenant_id' => $caixa->tenant_id,
                        ]);
                    }

                    // registrar pagamentos como movimentos e ajustar totais do caixa
                    if ($pagamentos && is_array($pagamentos) && count($pagamentos) > 0) {
                        foreach ($pagamentos as $p) {
                            $forma = $p['forma'] ?? 'dinheiro';
                            $valor = floatval($p['valor'] ?? 0);
                            if ($valor <= 0) continue;

                            CaixaMovimento::create([
                                'caixa_id' => $caixa->id,
                                'user_id' => $user->id,
                                'tenant_id' => $caixa->tenant_id,
                                'tipo' => 'pagamento',
                                'valor' => $valor,
                                'forma_pagamento' => $forma,
                                'invoice_id' => $invoice->id,
                                'obs' => 'Pagamento PDV'
                            ]);

                            $caixa->increment('total_vendas', $valor);
                            $col = 'total_' . $forma;
                            if (in_array($col, ['total_dinheiro','total_pix','total_debito','total_credito'])) {
                                $caixa->increment($col, $valor);
                            }
                        }
                    } else {
                        // fallback: único pagamento
                        $valor = $totalFinal;
                        CaixaMovimento::create([
                            'caixa_id' => $caixa->id,
                            'user_id' => $user->id,
                            'tenant_id' => $caixa->tenant_id,
                            'tipo' => 'pagamento',
                            'valor' => $valor,
                            'forma_pagamento' => $formaPagamento,
                            'invoice_id' => $invoice->id,
                            'obs' => 'Pagamento PDV'
                        ]);
                        $caixa->increment('total_vendas', $valor);
                        $col = 'total_' . $formaPagamento;
                        if (in_array($col, ['total_dinheiro','total_pix','total_debito','total_credito'])) {
                            $caixa->increment($col, $valor);
                        }
                    }
                }
            });
        } catch (\Exception $e) {
            Log::warning('Falha ao persistir venda local: ' . $e->getMessage());
        }

        // Incrementa contador do Free
try {
    if (($plan ?? 'free') === 'free') {
        $usage->increment('sales_count');
    }
} catch (\Exception $e) {
    \Log::warning('Erro ao incrementar uso: '.$e->getMessage());
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