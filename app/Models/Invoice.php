<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id_dolibarr',
        'caixa_id',
        'tenant_id',
        'user_id',
        'total',
        'forma_pagamento',
        'invoice_date',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'invoice_date' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function caixa()
    {
        return $this->belongsTo(Caixa::class);
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
