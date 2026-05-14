<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Cria o usuário no Laravel
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 2. Cria o Tenant/Loja no Laravel - LEI 1: DE QUAL LOJA É
        $tenant = Tenant::create([
            'name' => $request->name,
            'email' => $request->email,
            'dolibarr_db' => 'vendafacil_dolibarr', // Mesmo banco pra 500 lojas
            'dolibarr_url' => env('DOLIBARR_BASE_URL'),
            'plan' => 'basico',
            'api_key' => Str::random(40), // Gera chave única da loja
        ]);

        // 3. Associa user ao tenant
        $user->tenant_id = $tenant->id;
        $user->save();

        // 4. Cria dados iniciais no Dolibarr já isolados
        $this->criarDadosIniciaisDolibarr($tenant);

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * LEI 1: Toda criação no Dolibarr já nasce com options_loja_id
     */
    private function criarDadosIniciaisDolibarr(Tenant $tenant)
    {
        $api = Http::withHeaders([
            'DOLAPIKEY' => env('DOLIBARR_API_KEY')
        ])->baseUrl(env('DOLIBARR_BASE_URL').'/api/index.php');

        // 1. Cria Armazém da loja
        $api->post('/warehouses', [
            'label' => 'Estoque - '.$tenant->name,
            'description' => 'Armazém principal',
            'array_options' => [
                'options_loja_id' => $tenant->id
            ]
        ]);

        // 2. Cria Produto inicial da loja
        $api->post('/products', [
            'ref' => 'INICIAL-'.$tenant->id,
            'label' => 'Produto Inicial '.$tenant->name,
            'type' => '0', // 0 = Produto, 1 = Serviço
            'status' => '1', // 1 = À venda
            'array_options' => [
                'options_loja_id' => $tenant->id
            ]
        ]);

        // 3. Cria Usuário no Dolibarr com mesmo email
        $api->post('/users', [
            'login' => $tenant->email,
            'lastname' => $tenant->name,
            'email' => $tenant->email,
            'array_options' => [
                'options_loja_id' => $tenant->id
            ]
        ]);
    }
}