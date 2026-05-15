<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_dolibarr')->unique();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};