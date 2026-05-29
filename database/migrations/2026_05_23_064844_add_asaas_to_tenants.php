<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('asaas_customer_id')->nullable()->after('plan');
            $table->string('asaas_subscription_id')->nullable()->after('asaas_customer_id');
            $table->date('next_due_date')->nullable();
            $table->string('plan_status')->default('active');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['asaas_customer_id','asaas_subscription_id','next_due_date','plan_status']);
        });
    }
};