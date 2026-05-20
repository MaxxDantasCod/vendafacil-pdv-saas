<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaixaMovimento extends Model
{
    protected $fillable = [
        'caixa_id', 
        'user_id', 
        'tenant_id',
        'tipo', 
        'valor', 
        'forma_pagamento', 
        'invoice_id', 
        'obs'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
    ];

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}