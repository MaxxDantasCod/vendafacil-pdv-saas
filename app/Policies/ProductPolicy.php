<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->role === 'superadmin') {
            return true;
        }
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['dono', 'funcionario']);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->tenant_id === $product->tenant_id && 
               in_array($user->role, ['dono', 'funcionario']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['dono', 'funcionario']);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->role === 'dono' && $user->tenant_id === $product->tenant_id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->role === 'dono' && $user->tenant_id === $product->tenant_id;
    }

    public function restore(User $user, Product $product): bool
    {
        return $user->role === 'dono' && $user->tenant_id === $product->tenant_id;
    }

    public function forceDelete(User $user, Product $product): bool
    {
        return $user->role === 'dono' && $user->tenant_id === $product->tenant_id;
    }
}