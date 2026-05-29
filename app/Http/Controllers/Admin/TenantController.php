<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(): View
    {
        $tenants = Tenant::orderByDesc('created_at')->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email',
            'dolibarr_db' => 'required|string|max:255',
            'dolibarr_url' => 'required|url|max:255',
            'plan' => 'required|in:free,pro,enterprise',
            'api_key' => 'nullable|string',
            'plan_status' => 'required|in:active,pending,overdue,cancelled',
        ]);

        Tenant::create($data);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Loja criada com sucesso!');
    }

    public function show(Tenant $tenant): View
    {
        $tenant->load('users');

        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email,' . $tenant->id,
            'dolibarr_db' => 'required|string|max:255',
            'dolibarr_url' => 'required|url|max:255',
            'plan' => 'required|in:free,pro,enterprise',
            'api_key' => 'nullable|string',
            'plan_status' => 'required|in:active,pending,overdue,cancelled',
        ]);

        $tenant->update($data);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Loja atualizada com sucesso!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Loja removida com sucesso.');
    }
}
