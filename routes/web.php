<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\TakePOSController;
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
    Route::get('/caixa', [App\Http\Controllers\CaixaController::class, 'index'])->name('caixa.index'); //Nova Rota para o PDV Isolado
/*  Route::get('/pdv', [App\Http\Controllers\PdvController::class, 'index'])->name('pdv.index');
    Route::get('/pdv/buscar-produto', [App\Http\Controllers\PdvController::class, 'buscarProduto'])->name('pdv.buscar-produto');
    Route::get('/pdv/buscar-cliente', [App\Http\Controllers\PdvController::class, 'buscarCliente'])->name('pdv.buscar-cliente');
//  Route::post('/pdv/finalizar', [App\Http\Controllers\PdvController::class, 'finalizarVenda'])->name('pdv.finalizar');
    Route::post('/pdv/finalizar-venda', [App\Http\Controllers\PdvController::class, 'finalizarVenda'])->name('pdv.finalizar');
    Route::get('/teste-tenant', function () {
    if (!auth()->check()) return 'Não logado';
    return 'Loja ID: ' . auth()->user()->tenant_id;
})->middleware('auth');
}); 

    Route::get('/pdv', [App\Http\Controllers\PdvController::class, 'index'])->name('pdv.index');
    Route::get('/pdv/buscar-produto', [App\Http\Controllers\PdvController::class, 'buscarProduto'])->name('pdv.buscar-produto');
    Route::get('/pdv/buscar-cliente', [App\Http\Controllers\PdvController::class, 'buscarCliente'])->name('pdv.buscar-cliente');
    Route::post('/pdv/finalizar-venda', [App\Http\Controllers\PdvController::class, 'finalizarVenda'])->name('pdv.finalizar'); */
    });

    Route::middleware(['auth', 'role:superadmin'])->group(function () {
    Route::resource('tenants', TenantController::class);
});

    Route::middleware(['auth', 'role:dono,superadmin'])->group(function () {
    Route::resource('users', UserController::class);
});

    Route::middleware(['auth', 'role:funcionario,dono,superadmin'])->group(function () {
    Route::resource('products', ProductController::class);
});

// PDV ISOLADO - TELA CHEIA
Route::middleware(['auth'])->group(function () {
    Route::get('/caixa', [App\Http\Controllers\CaixaController::class, 'index'])->name('caixa.index');
    Route::post('/caixa/abrir', [App\Http\Controllers\CaixaController::class, 'abrir'])->name('caixa.abrir');
    Route::post('/caixa/fechar', [App\Http\Controllers\CaixaController::class, 'fechar'])->name('caixa.fechar');
    Route::get('/caixa/relatorio-dia', [App\Http\Controllers\CaixaController::class, 'relatorioDia'])->name('caixa.relatorio');
    
    // APIs do PDV reaproveitadas
    Route::get('/caixa/buscar-produto', [App\Http\Controllers\CaixaController::class, 'buscarProduto'])->name('caixa.buscar-produto');
    Route::get('/caixa/buscar-cliente', [App\Http\Controllers\CaixaController::class, 'buscarCliente'])->name('caixa.buscar-cliente');
    Route::post('/caixa/finalizar-venda', [App\Http\Controllers\CaixaController::class, 'finalizarVenda'])->name('caixa.finalizar');
});
require __DIR__.'/auth.php';
