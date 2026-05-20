<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caixa_movimentos', function (Blueprint $table) {
    $table->id();
    $table->foreignId('caixa_id')->constrained()->onDelete('cascade');
    $table->foreignId('user_id')->constrained();
    $table->foreignId('tenant_id')->constrained();
    $table->string('tipo');
    $table->decimal('valor', 10, 2);
    $table->string('forma_pagamento')->nullable();
    $table->integer('invoice_id')->nullable();
    $table->text('obs')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('caixa_movimentos');
    }
};