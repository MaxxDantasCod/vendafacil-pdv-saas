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
            ['email' => 'admin@vendafacil.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('admin123'),
            ]
        );

        Tenant::query()->updateOrCreate(
            ['email' => 'admin@vendafacil.com'],
            [
                'name' => 'Loja principal',
                'dolibarr_db' => 'dolibarr_vendafacil',
                'dolibarr_url' => 'http://localhost/VendaFacilPDV',
                'plan' => 'pro',
                'api_key' => config('dolibarr.api_key') ?? '',
            ]
        );
    }
}
