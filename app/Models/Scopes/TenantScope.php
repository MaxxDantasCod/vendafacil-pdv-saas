<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
    if (auth()->check() && auth()->user()->role !== 'superadmin') {
        $builder->where($model->getTable() . '.tenant_id', auth()->user()->tenant_id);
    }
    }

        // Superadmin vê tudo
        if (auth()->user()->role === 'superadmin') {
            return;
        }

        // Dono ou funcionario: só vê o tenant dele
        if (auth()->user()->tenant_id) {
            $builder->where($model->getTable() . '.tenant_id', auth()->user()->tenant_id);
        } else {
            $builder->whereRaw('1 = 0');
        }
    }
}