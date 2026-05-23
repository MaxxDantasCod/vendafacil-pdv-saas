<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Caixa;
use App\Models\CaixaMovimento;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CaixaController extends Controller
{
 public function index()
{
    $user = auth()->user();
    
    $plan = $user->tenant->plan ?? 'free';
    if (in_array($plan, ['basico','basic'])) $plan = 'free';
    
    $planLimit = $plan === 'free' ? 50 : null;
    $planUsage = 0;
    
    if ($planLimit) {
        $planUsage = \App\Models\SalesUsageMonthly::where('user_id', $user->id)
            ->where('year_month', now()->format('Y-m'))
            ->value('sales_count') ?? 0;
    }

    $caixaAberto = \App\Models\Caixa::where('user_id', $user->id)
        ->where('status', 'aberto')
        ->exists();

    return view('caixa.index', [
        'caixaAberto' => $caixaAberto,
        'planUsage'   => $planUsage,
        'planLimit'   => $planLimit,
    ]);
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
    try {
        $user = auth()->user();
        
        $query = Caixa::where('user_id', $user->id)
            ->whereDate('aberto_em', today())
            ->latest();

        // Só filtra por tenant se a coluna existir
        if (Schema::hasColumn('caixas', 'tenant_id')) {
            $query->where('tenant_id', $user->tenant_id);
        }

        $caixa = $query->first();

        if (!$caixa) {
            return response()->json(['error' => 'Nenhum caixa aberto hoje'], 404);
        }

        // Sangria
        $caixa->total_sangria = CaixaMovimento::where('caixa_id', $caixa->id)
            ->where('tipo', 'sangria')
            ->when(Schema::hasColumn('caixa_movimentos', 'tenant_id'), function ($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })
            ->sum('valor');

        // Suprimento  
        $caixa->total_suprimento = CaixaMovimento::where('caixa_id', $caixa->id)
            ->where('tipo', 'suprimento')
            ->when(Schema::hasColumn('caixa_movimentos', 'tenant_id'), function ($q) use ($user) {
                $q->where('tenant_id', $user->tenant_id);
            })
            ->sum('valor');

        // Se tiver Invoice, calcula vendas. Se não, pula.
        if (class_exists('App\Models\Invoice')) {
            $caixa->total_vendas = Invoice::where('caixa_id', $caixa->id)->sum('total');
            $caixa->total_dinheiro = Invoice::where('caixa_id', $caixa->id)->where('forma_pagamento', 'dinheiro')->sum('total');
            $caixa->total_pix = Invoice::where('caixa_id', $caixa->id)->where('forma_pagamento', 'pix')->sum('total');
            $caixa->total_debito = Invoice::where('caixa_id', $caixa->id)->where('forma_pagamento', 'debito')->sum('total');
            $caixa->total_credito = Invoice::where('caixa_id', $caixa->id)->where('forma_pagamento', 'credito')->sum('total');
        }

        return response()->json($caixa);

    } catch (\Exception $e) {
        Log::error('Erro relatorioDia: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }

    }

    // Buscar caixas por intervalo de datas (consulta por tenant)
    public function buscarCaixas(Request $request)
    {
        $request->validate([
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ]);

        $user = auth()->user();
        $query = Caixa::query()->where('tenant_id', $user->tenant_id);

        if ($request->filled('start')) {
            $query->whereDate('aberto_em', '>=', $request->start);
        }

        if ($request->filled('end')) {
            $query->whereDate('aberto_em', '<=', $request->end);
        }

        if (!$request->filled('start') && !$request->filled('end')) {
            $query->whereDate('aberto_em', today());
        }

        $caixas = $query
            ->with('user:id,name')
            ->withCount('invoices')
            ->withSum(['invoices as total_dinheiro' => function ($q) {
                $q->where('forma_pagamento', 'dinheiro');
            }], 'total')
            ->withSum(['invoices as total_pix' => function ($q) {
                $q->where('forma_pagamento', 'pix');
            }], 'total')
            ->withSum(['invoices as total_debito' => function ($q) {
                $q->where('forma_pagamento', 'debito');
            }], 'total')
            ->withSum(['invoices as total_credito' => function ($q) {
                $q->where('forma_pagamento', 'credito');
            }], 'total')
            // Somas a partir dos movimentos (registrados pelo PDV)
            ->withSum(['movimentos as total_dinheiro_mov' => function ($q) {
                $q->where('tipo', 'pagamento')->where('forma_pagamento', 'dinheiro');
            }], 'valor')
            ->withSum(['movimentos as total_pix_mov' => function ($q) {
                $q->where('tipo', 'pagamento')->where('forma_pagamento', 'pix');
            }], 'valor')
            ->withSum(['movimentos as total_debito_mov' => function ($q) {
                $q->where('tipo', 'pagamento')->where('forma_pagamento', 'debito');
            }], 'valor')
            ->withSum(['movimentos as total_credito_mov' => function ($q) {
                $q->where('tipo', 'pagamento')->where('forma_pagamento', 'credito');
            }], 'valor')
            ->withSum(['movimentos as total_sangria' => function ($q) {
                $q->where('tipo', 'sangria');
            }], 'valor')
            ->withSum(['movimentos as total_suprimento' => function ($q) {
                $q->where('tipo', 'suprimento');
            }], 'valor')
            ->orderBy('aberto_em', 'desc')
            ->get();

        return response()->json($caixas);
    }
    // Listar vendas (invoices + items) de um caixa específico
    public function vendasCaixa(Caixa $caixa)
    {
        $user = auth()->user();

        if ($caixa->tenant_id !== $user->tenant_id) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $invoices = $caixa->invoices()->with('items')->orderBy('created_at', 'desc')->get();

        return response()->json($invoices);
    }

    // Página de relatórios (UI)
    public function relatoriosPage()
    {
        return view('caixa.relatorios');
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
        $p['stock_quantity'] = $vinculo ? $vinculo->stock_quantity : null;
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

    // SANGRIA E SUPRIMENTOS

public function sangria(Request $request)
{
    \Log::info('Sangria chamada', ['user_id' => auth()->id(), 'valor' => $request->valor]);
    
    if (!auth()->check()) {
        return response()->json(['error' => 'Usuário não autenticado'], 401);
    }
    
    $request->validate([
        'valor' => 'required|numeric|min:0.01',
        'obs' => 'nullable|string|max:255'
    ]);

    $user = auth()->user();
    $caixa = Caixa::where('user_id', $user->id)
        ->where('tenant_id', $user->tenant_id)
        ->where('status', 'aberto')
        ->first();

    if (!$caixa) {
        return response()->json(['error' => 'Abra o caixa antes de fazer sangria'], 400);
    }

    $valor = (float)$request->valor;

    if ($caixa->total_dinheiro < $valor) {
        return response()->json(['error' => 'Valor de sangria maior que o disponível em dinheiro'], 400);
    }

    try {
        DB::transaction(function () use ($caixa, $valor, $user, $request) {
            $caixa->increment('total_sangria', $valor);
            $caixa->decrement('total_dinheiro', $valor);

            CaixaMovimento::create([
                'caixa_id' => $caixa->id,
                'user_id' => $user->id,
                'tenant_id' => $caixa->tenant_id,
                'tipo' => 'sangria',
                'valor' => $valor,
                'forma_pagamento' => 'dinheiro',
                'invoice_id' => null,
                'obs' => $request->obs ?? 'Sangria PDV'
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Sangria realizada com sucesso']);

    } catch (\Exception $e) {
        \Log::error('Erro sangria: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return response()->json(['error' => 'Erro ao processar sangria: ' . $e->getMessage()], 500);
    }
}
public function suprimento(Request $request)
{
    $request->validate([
        'valor' => 'required|numeric|min:0.01',
        'obs' => 'nullable|string|max:255'
    ]);

    $user = auth()->user();
    $caixa = Caixa::where('user_id', $user->id)
        ->where('tenant_id', $user->tenant_id)
        ->where('status', 'aberto')
        ->first();

    if (!$caixa) {
        return response()->json(['error' => 'Abra o caixa antes de fazer suprimento'], 400);
    }

    $valor = (float)$request->valor;

    try {
        DB::transaction(function () use ($caixa, $valor, $user, $request) {
            $caixa->increment('total_suprimento', $valor);
            $caixa->increment('total_dinheiro', $valor);

            CaixaMovimento::create([
                'caixa_id' => $caixa->id,
                'user_id' => $user->id,
                'tenant_id' => $caixa->tenant_id,
                'tipo' => 'suprimento',
                'valor' => $valor,
                'forma_pagamento' => 'dinheiro',
                'invoice_id' => null,
                'obs' => $request->obs ?? 'Suprimento PDV'
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Suprimento realizado com sucesso']);

    } catch (\Exception $e) {
        \Log::error('Erro suprimento: ' . $e->getMessage());
        return response()->json(['error' => 'Erro ao processar suprimento'], 500);
    }
}
    // FINALIZAR VENDA - Copiado do PdvController + atualiza caixa
    public function finalizarVenda(Request $request)
    {
        try {
            // BLOQUEIO PLANO FREE
$user = auth()->user();
$plan = $user->tenant->plan ?? 'free';
if (in_array($plan, ['basico','basic'])) $plan = 'free';

if ($plan === 'free') {
    $usage = \App\Models\SalesUsageMonthly::firstOrCreate(
        ['user_id' => $user->id, 'year_month' => now()->format('Y-m')],
        ['sales_count' => 0]
    );
    
    if ($usage->sales_count >= 50) {
        return response()->json([
            'error' => 'Limite do plano Free atingido (50 vendas/mês). Faça upgrade para continuar.'
        ], 403);
    }
}
            $user = auth()->user();
            $caixa = Caixa::where('user_id', $user->id)->where('status', 'aberto')->first();
            
            if ($plan === 'free') {
            $usage->increment('sales_count');
          }

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

            $produtosLocal = [];
            foreach ($carrinho as $item) {
                $produtoLocal = Produto::where('tenant_id', $user->tenant_id)
                    ->where('id_dolibarr', $item['id'])
                    ->first();

                if (!$produtoLocal) {
                    return response()->json(['error' => "Produto {$item['nome']} não está vinculado à sua loja."], 400);
                }

                if ($produtoLocal->stock_quantity !== null && $produtoLocal->stock_quantity < $item['qtd']) {
                    return response()->json(['error' => "Estoque insuficiente para {$item['nome']}. Disponível: {$produtoLocal->stock_quantity}"], 400);
                }

                $produtosLocal[$item['id']] = $produtoLocal;
            }

            // 3. ADICIONA LINHAS
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

            // 4. VALIDA
            Http::withHeaders(['DOLAPIKEY' => env('DOLIBARR_API_KEY')])
                ->post(env('DOLIBARR_BASE_URL'). '/api/index.php/invoices/'. $invoiceId. '/validate', ['notrigger' => 0]);

            // 5. ATUALIZA ESTOQUE E CAIXA
            $totalFinal = $totalBruto - ($descontoTipo == 'valor' ? $desconto : ($totalBruto * $desconto / 100));

            DB::transaction(function () use ($carrinho, $caixa, $produtosLocal, $formaPagamento, $totalFinal, $user, $invoiceId) {
                foreach ($carrinho as $item) {
                    $produtoLocal = $produtosLocal[$item['id']];

                    if ($produtoLocal->stock_quantity !== null) {
                        $updated = Produto::where('id', $produtoLocal->id)
                            ->where('stock_quantity', '>=', $item['qtd'])
                            ->decrement('stock_quantity', $item['qtd']);

                        if ($updated === 0) {
                            throw new \Exception("Estoque insuficiente para {$produtoLocal->ref_loja}");
                        }
                    }
                }

                // Persist local invoice and items for consulta
                $invoice = Invoice::create([
                    'invoice_id_dolibarr' => $invoiceId,
                    'caixa_id' => $caixa->id,
                    'tenant_id' => $caixa->tenant_id,
                    'user_id' => $user->id,
                    'total' => $totalFinal,
                    'forma_pagamento' => $formaPagamento,
                    'invoice_date' => now(),
                ]);

                foreach ($carrinho as $item) {
                    $prodLocal = $produtosLocal[$item['id']];
                    \App\Models\InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'product_id_dolibarr' => $item['id'],
                        'nome' => $item['nome'],
                        'ref_loja' => $prodLocal ? $prodLocal->ref_loja : null,
                        'qtd' => (int)$item['qtd'],
                        'preco' => (float)$item['preco'],
                        'total' => (float)($item['preco'] * $item['qtd']),
                        'tenant_id' => $caixa->tenant_id,
                    ]);
                }

                $caixa->increment('total_vendas', $totalFinal);
                $caixa->increment('total_'. $formaPagamento, $totalFinal);
            });

            // Invalidate dashboard cache for this tenant for the current hour so metrics refresh
            try {
                $cacheKey = sprintf('dashboard_metrics:tenant:%s:%s', $caixa->tenant_id, now()->format('Y-m-d-H'));
                Cache::forget($cacheKey);
            } catch (\Throwable $e) {
                Log::warning('Failed to clear dashboard cache', ['tenant_id' => $caixa->tenant_id, 'error' => $e->getMessage()]);
            }

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