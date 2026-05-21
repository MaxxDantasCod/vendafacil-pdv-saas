<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->bigInteger('product_id_dolibarr')->nullable()->index();
            $table->string('nome')->nullable();
            $table->string('ref_loja')->nullable();
            $table->integer('qtd')->default(1);
            $table->decimal('preco', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
