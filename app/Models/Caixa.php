<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    protected $fillable = ['nome', 'saldo', 'tenant_id']; // ajusta os campos

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\TenantScope());
        
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