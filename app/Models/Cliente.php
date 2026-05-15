<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = ['id_dolibarr', 'tenant_id'];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\TenantScope());
        
        static::creating(function ($model) {
            if (auth()->check() && auth()->user()->role !== 'superadmin') {
                $model->tenant_id = auth()->user()->tenant_id;
            }
        });
    }
}