<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'price', 
        'tenant_id',
        'dolibarr_id'
    ];

    protected static function booted(): void
    {
        // Aplica o filtro LEI 2 em toda query
        static::addGlobalScope(new TenantScope);
        
        // Quando criar produto, já injeta o tenant_id do user logado
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}