<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'maxsuell-santos@live.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('Maxxmaxx.1'),
                'role' => 'superadmin',
                'tenant_id' => null,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'admin@vendafacil.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
                'role' => 'superadmin',
                'tenant_id' => null,
            ]
        );

        Tenant::query()->updateOrCreate(
            ['email' => 'admin@vendafacil.com'],
            [
                'name' => 'Loja principal',
                'dolibarr_db' => 'dolibarr_vendafacil',
                'dolibarr_url' => 'http://localhost/VendaFacilPDV',
                'plan' => 'pro',
                'plan_status' => 'active',
                'api_key' => config('dolibarr.api_key') ?? '',
            ]
        );
    }
}
