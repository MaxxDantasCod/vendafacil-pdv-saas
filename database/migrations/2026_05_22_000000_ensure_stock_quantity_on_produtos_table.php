<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (!Schema::hasColumn('produtos', 'stock_quantity')) {
                $table->integer('stock_quantity')
                    ->nullable()
                    ->after('ref_loja')
                    ->comment('Quantidade disponível em estoque');
            }
        });
    }

    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            if (Schema::hasColumn('produtos', 'stock_quantity')) {
                $table->dropColumn('stock_quantity');
            }
        });
    }
};
