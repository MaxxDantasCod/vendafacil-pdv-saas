<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    // superadmin pode tudo
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'superadmin') {
            return true;
        }
        return null;
    }

    // Quem pode ver a lista
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['funcionario', 'dono']);
    }

    // Quem pode ver 1 produto específico
    public function view(User $user, Product $product): bool
    {
        return $user->tenant_id === $product->tenant_id;
    }

    // Quem pode criar
    public function create(User $user): bool
    {
        return in_array($user->role, ['funcionario', 'dono']);
    }

    // Quem pode editar
    public function update(User $user, Product $product): bool
    {
        return $user->tenant_id === $product->tenant_id 
            && in_array($user->role, ['dono']); // só dono edita
    }

    // Quem pode deletar
    public function delete(User $user, Product $product): bool
    {
        return $user->tenant_id === $product->tenant_id 
            && $user->role === 'dono'; // só dono deleta
    }
}