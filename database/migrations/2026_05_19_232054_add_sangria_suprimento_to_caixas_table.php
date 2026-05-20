<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->decimal('total_sangria', 10, 2)->default(0)->after('total_credito');
            $table->decimal('total_suprimento', 10, 2)->default(0)->after('total_sangria');
        });
    }

    public function down(): void
    {
        Schema::table('caixas', function (Blueprint $table) {
            $table->dropColumn(['total_sangria', 'total_suprimento']);
        });
    }
};