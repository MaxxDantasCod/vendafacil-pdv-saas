<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EstoqueController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\TakePOSController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('guest');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
//    Route::get('/pdv', TakePOSController::class)->name('pdv.show');
//    Route::view('/produtos', 'placeholders.products')->name('products.index');
    Route::view('/clientes', 'placeholders.customers')->name('customers.index');
    Route::view('/relatorios', 'placeholders.reports')->name('reports.index');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Rotas para produtos (Maxsuell Dantas)
    Route::get('/produtos', [App\Http\Controllers\ProdutoController::class, 'index'])->name('produtos.index');
    Route::get('/produtos/criar', [ProdutoController::class, 'create'])->name('produtos.create');
    Route::post('/produtos', [ProdutoController::class, 'store'])->name('produtos.store');
    Route::get('/produtos/{id}/editar', [App\Http\Controllers\ProdutoController::class, 'edit'])->name('produtos.edit');
    Route::put('/produtos/{id}', [App\Http\Controllers\ProdutoController::class, 'update'])->name('produtos.update');
    Route::delete('/produtos/{id}', [App\Http\Controllers\ProdutoController::class, 'destroy'])->name('produtos.destroy');
    // Rotas para clientes
    Route::get('/clientes', [App\Http\Controllers\ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/criar', [App\Http\Controllers\ClienteController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [App\Http\Controllers\ClienteController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}/editar', [App\Http\Controllers\ClienteController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'update'])->name('clientes.update');
    Route::delete('/clientes/{id}', [App\Http\Controllers\ClienteController::class, 'destroy'])->name('clientes.destroy');
    // Rotas do PDV
    // O PDV isolado e APIs já são definidas abaixo no grupo auth/tenant
});

Route::middleware(['auth', 'role:superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('tenants', AdminTenantController::class);
    Route::resource('users', AdminUserController::class)->except(['show']);
});

// PDV ISOLADO - TELA CHEIA
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/caixa', [App\Http\Controllers\CaixaController::class, 'index'])->name('caixa.index');
    Route::post('/caixa/abrir', [App\Http\Controllers\CaixaController::class, 'abrir'])->name('caixa.abrir');
    Route::post('/caixa/fechar', [App\Http\Controllers\CaixaController::class, 'fechar'])->name('caixa.fechar');
    Route::get('/caixa/relatorio-dia', [App\Http\Controllers\CaixaController::class, 'relatorioDia'])->name('caixa.relatorio');
        Route::get('/caixa/relatorios', [App\Http\Controllers\CaixaController::class, 'relatoriosPage'])->name('caixa.relatorios');
    Route::get('/caixa/buscar-caixas', [App\Http\Controllers\CaixaController::class, 'buscarCaixas'])->name('caixa.buscar-caixas');
    Route::get('/caixa/{caixa}/vendas', [App\Http\Controllers\CaixaController::class, 'vendasCaixa'])->name('caixa.vendas');
    
    // APIs do Caixa (PDV)
    Route::get('/caixa/buscar-produto', [App\Http\Controllers\CaixaController::class, 'buscarProduto'])->name('caixa.buscar-produto');
    Route::get('/caixa/buscar-cliente', [App\Http\Controllers\CaixaController::class, 'buscarCliente'])->name('caixa.buscar-cliente');
    Route::post('/caixa/finalizar-venda', [App\Http\Controllers\CaixaController::class, 'finalizarVenda'])->name('caixa.finalizar');

    // Sangria e Suprimento
    Route::post('/caixa/sangria', [App\Http\Controllers\CaixaController::class, 'sangria'])->name('caixa.sangria');
    Route::post('/caixa/suprimento', [App\Http\Controllers\CaixaController::class, 'suprimento'])->name('caixa.suprimento');

    // Estoque local
    Route::get('/estoque', [EstoqueController::class, 'index'])->name('estoque.index');
    Route::get('/estoque/{produto}/editar', [EstoqueController::class, 'edit'])->name('estoque.edit');
    Route::put('/estoque/{produto}', [EstoqueController::class, 'update'])->name('estoque.update');

    // Dados Cliente
    Route::get('/planos/dados', [PlanController::class, 'dados'])->name('planos.dados');
    Route::post('/planos/dados', [PlanController::class, 'salvarDados'])->name('planos.dados.salvar');

    // Planos e Financeiro
    Route::middleware(['auth'])->group(function () {
    Route::get('/planos', [PlanController::class, 'index'])->name('planos.index');
    Route::get('/planos/upgrade/{plan}', [PlanController::class, 'upgrade'])->name('planos.upgrade');
    Route::get('/faturas', [PlanController::class, 'faturas'])->name('planos.faturas');
    Route::get('/minha-assinatura', [PlanController::class, 'assinatura'])->name('planos.assinatura');
    Route::post('/minha-assinatura/cancelar', [PlanController::class, 'cancelar'])->name('planos.cancelar');
    });
});

    Route::post('/webhook/asaas', [WebhookController::class, 'handle']);
    Route::view('/politica-privacidade', 'legal.privacidade')->name('privacidade');

require __DIR__.'/auth.php';