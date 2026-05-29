<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AsaasService;

class PlanController extends Controller
{
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $usage = 32;
        return view('planos.index', [
            'currentPlan' => $tenant->plan,
            'usage' => $usage
        ]);
    }

    public function upgrade($plan)
    {
        $user = auth()->user()->fresh();

        if (empty($user->cpf_cnpj)) {
            return redirect()->route('planos.dados', ['plan' => $plan])
                ->with('plan_escolhido', $plan);
        }

        $tenant = $user->tenant;
        $asaas = new AsaasService();

        if (!$tenant->asaas_customer_id) {
            $customer = $asaas->customer($user);
            if (!isset($customer['id'])) {
                return back()->withErrors(['asaas' => 'Erro Asaas: '.($customer['errors'][0]['description']?? 'tente novamente')]);
            }
            $tenant->update(['asaas_customer_id' => $customer['id']]);
        }

        $sub = $asaas->subscription($tenant->asaas_customer_id, $plan);
        $tenant->update([
            'asaas_subscription_id' => $sub['id'],
            'next_due_date' => $sub['nextDueDate'],
            'plan_status' => 'pending'
        ]);

        return redirect($sub['invoiceUrl']);
    }

    public function dados(Request $request)
    {
        $plan = session('plan_escolhido')?? $request->get('plan', 'pro');
        return view('planos.dados', compact('plan'));
    }

    public function salvarDados(Request $request)
{
    $request->validate([
        'cpf_cnpj' => 'required|min:11',
        'plan' => 'required|in:pro,enterprise'
    ]);

    $cpfLimpo = preg_replace('/\D/', '', $request->cpf_cnpj);

    // VALIDAÇÃO REAL
    if (!$this->validaCpfCnpj($cpfLimpo)) {
        return back()->withErrors(['cpf_cnpj' => 'CPF/CNPJ inválido. Verifique os dígitos.'])->withInput();
    }

    $user = auth()->user();
    $user->forceFill(['cpf_cnpj' => $cpfLimpo])->save();

    if (!$user->fresh()->cpf_cnpj) {
        return back()->withErrors(['cpf_cnpj' => 'Erro ao salvar. Tente novamente.']);
    }

    return redirect()
        ->route('planos.upgrade', $request->plan)
        ->with('success', 'CPF salvo com segurança! Redirecionando para pagamento...');
}

// ADICIONA ESTE MÉTODO PRIVADO NO FINAL DA CLASSE
private function validaCpfCnpj($numero)
{
    $numero = preg_replace('/\D/', '', $numero);

    if (strlen($numero) === 11) {
        // VALIDA CPF
        if (preg_match('/(\d)\1{10}/', $numero)) return false;
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $numero[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($numero[$c]!= $d) return false;
        }
        return true;
    }

    if (strlen($numero) === 14) {
        // VALIDA CNPJ
        if (preg_match('/(\d)\1{13}/', $numero)) return false;
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $numero[$i] * $j;
            $j = ($j == 2)? 9 : $j - 1;
        }
        $resto = $soma % 11;
        if ($numero[12]!= ($resto < 2? 0 : 11 - $resto)) return false;

        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $numero[$i] * $j;
            $j = ($j == 2)? 9 : $j - 1;
        }
        $resto = $soma % 11;
        return $numero[13] == ($resto < 2? 0 : 11 - $resto);
    }

    return false;
}
    public function assinatura()
    {
        $tenant = auth()->user()->tenant;
        $asaas = new AsaasService();

        $faturas = [];
        if ($tenant->asaas_customer_id) {
            $faturas = array_slice($asaas->invoices($tenant->asaas_customer_id)?? [], 0, 3);
        }

        return view('planos.assinatura', compact('tenant', 'faturas'));
    }

    public function cancelar(Request $request)
    {
        $tenant = auth()->user()->tenant;
        $asaas = new AsaasService();

        if ($tenant->asaas_subscription_id) {
            $asaas->cancel($tenant->asaas_subscription_id);
            $tenant->update(['plan_status' => 'cancelled']);
        }

        return back()->with('success', 'Assinatura cancelada. Você continua no Pro até o fim do período.');
    }
}