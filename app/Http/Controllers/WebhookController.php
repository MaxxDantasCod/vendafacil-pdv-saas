<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Log pra você ver o que o Asaas mandou
        Log::info('Asaas webhook', $request->all());

        $event = $request->input('event');
        $payment = $request->input('payment', []);

        // Acha o tenant pelo customer do Asaas
        $tenant = Tenant::where('asaas_customer_id', $payment['customer'] ?? null)->first();

        if (!$tenant) {
            return response()->json(['ok' => true]); // ignora se não achar
        }

        // Atualiza conforme o evento
        if (in_array($event, ['PAYMENT_CONFIRMED', 'PAYMENT_RECEIVED'])) {
            $valor = $payment['value'] ?? 0;
            $novoPlano = $valor >= 149 ? 'enterprise' : 'pro';
            
            $tenant->update([
                'plan' => $novoPlano,
                'plan_status' => 'active',
                'next_due_date' => now()->addMonth()->toDateString(),
            ]);
        }

        if ($event === 'PAYMENT_OVERDUE') {
            $tenant->update(['plan_status' => 'overdue']);
        }

        if ($event === 'PAYMENT_DELETED' || $event === 'SUBSCRIPTION_CANCELED') {
            $tenant->update([
                'plan' => 'free',
                'plan_status' => 'cancelled'
            ]);
        }

        return response()->json(['ok' => true]);
    }
}