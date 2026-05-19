<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->decimal('valor_inicial', 10, 2);
            $table->decimal('valor_final', 10, 2)->nullable();
            $table->decimal('total_vendas', 10, 2)->default(0);
            $table->decimal('total_dinheiro', 10, 2)->default(0);
            $table->decimal('total_pix', 10, 2)->default(0);
            $table->decimal('total_debito', 10, 2)->default(0);
            $table->decimal('total_credito', 10, 2)->default(0);
            $table->timestamp('aberto_em');
            $table->timestamp('fechado_em')->nullable();
            $table->enum('status', ['aberto', 'fechado'])->default('aberto');
            $table->text('obs_fechamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caixas');
    }
};