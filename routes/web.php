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
    Route::get('/pdv', TakePOSController::class)->name('pdv.show');
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
});

require __DIR__.'/auth.php';
