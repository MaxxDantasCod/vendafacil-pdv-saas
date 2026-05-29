<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'dolibarr_db',
        'dolibarr_url',
        'plan',
        'api_key',
        'dolibarr_warehouse_id',
        'dolibarr_societe_id',
    ];

    protected $casts = [
        'api_key' => 'encrypted',
    ];

    // Adiciona no final da classe
public function getStatusPtAttribute()
{
    return match($this->plan_status) {
        'active' => 'Ativo',
        'pending' => 'Aguardando pagamento',
        'overdue' => 'Pagamento atrasado',
        'cancelled' => 'Cancelado',
        default => 'Inativo'
    };
}

public function getPlanoPtAttribute()
{
    return match($this->plan) {
        'free' => 'Gratuito',
        'pro' => 'Pro',
        'enterprise' => 'Enterprise',
        default => ucfirst($this->plan)
    };
}

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
