<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('tenant')->orderByDesc('created_at')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        $tenants = Tenant::orderBy('name')->get();

        return view('admin.users.create', compact('tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:superadmin,dono,funcionario',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        if ($data['role'] === 'superadmin') {
            $data['tenant_id'] = null;
        }

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user): View
    {
        $tenants = Tenant::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'tenants'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:superadmin,dono,funcionario',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        if ($data['role'] === 'superadmin') {
            $data['tenant_id'] = null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Você não pode remover sua própria conta aqui.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário removido com sucesso.');
    }
}
