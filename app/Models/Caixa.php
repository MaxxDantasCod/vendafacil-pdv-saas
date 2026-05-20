<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caixa extends Model
{
    protected $fillable = [
        'user_id', 
        'tenant_id', 
        'valor_inicial', 
        'valor_final', 
        'total_vendas', 
        'total_dinheiro', 
        'total_pix', 
        'total_debito', 
        'total_credito', 
        'total_sangria',
        'total_suprimento',
        'aberto_em', 
        'fechado_em', 
        'status', 
        'obs_fechamento'
    ];

    protected $casts = [
        'aberto_em' => 'datetime',
        'fechado_em' => 'datetime',
        'valor_inicial' => 'decimal:2',
        'valor_final' => 'decimal:2',
        'total_vendas' => 'decimal:2',
        'total_dinheiro' => 'decimal:2',
        'total_pix' => 'decimal:2',
        'total_debito' => 'decimal:2',
        'total_credito' => 'decimal:2',
        'total_sangria' => 'decimal:2',
        'total_suprimento' => 'decimal:2',
    ];

    public function user() { 
        return $this->belongsTo(User::class); 
    }
    
    public function tenant() { 
        return $this->belongsTo(Tenant::class); 
    }

    public function movimentos() {
        return $this->hasMany(CaixaMovimento::class)->orderBy('created_at', 'desc');
    }
}