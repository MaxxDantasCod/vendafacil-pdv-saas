<?php
namespace App\Services;
use Illuminate\Support\Facades\Http;

class AsaasService
{
    protected $http;

    public function __construct()
    {
        $this->http = Http::withToken(config('services.asaas.key'))
            ->baseUrl(config('services.asaas.url'))
            ->acceptJson();
    }

    public function customer($user)
    {
        $res = $this->http->post('/customers', [
            'name' => $user->name,
            'email' => $user->email,
            'cpfCnpj' => $user->cpf_cnpj,
            'externalReference' => $user->tenant->id,
        ]);
        return $res->json();
    }

    public function subscription($customerId, $plan)
    {
        $values = ['pro' => 59, 'enterprise' => 149];
        return $this->http->post('/subscriptions', [
            'customer' => $customerId,
            'billingType' => 'UNDEFINED', // deixa cliente escolher Pix/Cartão
            'value' => $values[$plan],
            'nextDueDate' => now()->addDay()->format('Y-m-d'),
            'cycle' => 'MONTHLY',
            'description' => "Plano ".ucfirst($plan)." VendaFácil",
        ])->json();
    }

    public function invoices($customerId)
    {
        return $this->http->get('/payments', [
            'customer' => $customerId,
            'limit' => 50,
        ])->json('data');
    }

    public function cancel($subscriptionId)
    {
    return $this->http->delete("/subscriptions/{$subscriptionId}")->json();
    }
}