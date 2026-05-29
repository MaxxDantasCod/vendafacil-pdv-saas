<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'role',
        'cpf_cnpj',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'cpf_cnpj',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'cpf_cnpj' => 'encrypted',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function getCpfMascaradoAttribute()
    {
        if (!$this->cpf_cnpj) return null;

        $cpf = preg_replace('/\D/', '', $this->cpf_cnpj);
        return strlen($cpf) === 11
           ? '***.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-**'
            : '**.***.'.substr($cpf,5,3).'/****-**';
    }

    public function getIsSuperadminAttribute(): bool
    {
        return $this->role === 'superadmin';
    }
}
