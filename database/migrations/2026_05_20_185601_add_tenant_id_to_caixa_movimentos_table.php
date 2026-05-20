<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('caixa_movimentos', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->after('user_id');
            $table->index(['tenant_id', 'caixa_id']);
        });
    }

    public function down()
    {
        Schema::table('caixa_movimentos', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'caixa_id']);
            $table->dropColumn('tenant_id');
        });
    }
};