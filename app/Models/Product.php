<?php

namespace App\Models;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TenantScope;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'tenant_id', 'dolibarr_id'];

    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
        
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    protected static booted(): void
    {
    static::addGlobalScope(new TenantScope);
    
    static::creating(function ($model) {
        if (auth()->check() && auth()->user()->role !== 'superadmin') {
            $model->tenant_id = auth()->user()->tenant_id;
        }
    });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}