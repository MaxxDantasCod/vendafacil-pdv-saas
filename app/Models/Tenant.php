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
}
