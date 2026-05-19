<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsolaTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Lei 2: Se for superadmin, libera tudo
        if ($user->is_superadmin) {
            return $next($request);
        }

        // Lei 1: Lojista tem que ter tenant_id
        if (!$user->tenant_id) {
            abort(403, 'Sua conta não está vinculada a nenhuma loja.');
        }

        // Injeta o tenant_id na request pra usar nos controllers
        $request->attributes->set('tenant_id', $user->tenant_id);

        // Bloqueia lojista de acessar /admin
        if ($request->is('admin*')) {
            abort(403, 'Acesso negado.');
        }

        return $next($request);
    }
}