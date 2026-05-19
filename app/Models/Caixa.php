<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    protected $fillable = [
        'user_id', 'tenant_id', 'valor_inicial', 'valor_final', 
        'total_vendas', 'total_dinheiro', 'total_pix', 'total_debito', 
        'total_credito', 'aberto_em', 'fechado_em', 'status', 'obs_fechamento'
    ];

    protected $casts = [
        'aberto_em' => 'datetime',
        'fechado_em' => 'datetime',
    ];

    public function user() { 
        return $this->belongsTo(User::class); 
    }
    
    public function tenant() { 
        return $this->belongsTo(Tenant::class); 
    }
}