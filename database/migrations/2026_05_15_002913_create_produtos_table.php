<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_dolibarr')->unique()->comment('ID do produto no Dolibarr');
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete()->comment('ID da loja');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};