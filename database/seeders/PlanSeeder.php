<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::query()->updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'description' => 'Até 50 vendas por mês',
                'price_cents' => 0,
                'sales_limit_per_month' => 50,
                'is_active' => true,
            ]
        );

        Plan::query()->updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'description' => 'Vendas ilimitadas — R$ 49,90/mês',
                'price_cents' => 4990,
                'sales_limit_per_month' => null,
                'is_active' => true,
            ]
        );
    }
}
