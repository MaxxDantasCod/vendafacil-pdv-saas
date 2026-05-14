<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantScope
{
    public function handle(Request $request, Closure $next): Response
    {
        // Se não tá logado, deixa passar pro login
        if (!auth()->check()) {
            return $next($request);
        }

        // LEI 2: Todo query a partir daqui só enxerga dados da loja logada
        $tenantId = auth()->user()->tenant_id;

        if (!$tenantId) {
            abort(403, 'Usuário sem loja vinculada.');
        }

        // Compartilha com toda aplicação
        app()->instance('tenant_id', $tenantId);

        return $next($request);
    }
}