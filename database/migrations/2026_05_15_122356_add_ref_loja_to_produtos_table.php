<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->string('ref_loja')->after('id_dolibarr');
        });
    }

    public function down()
    {
        Schema::table('produtos', function (Blueprint $table) {
            $table->dropColumn('ref_loja');
        });
    }
};