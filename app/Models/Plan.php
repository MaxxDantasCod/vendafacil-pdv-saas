<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'price_cents',
        'sales_limit_per_month',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sales_limit_per_month' => 'integer',
        'price_cents' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function isUnlimitedSales(): bool
    {
        return $this->sales_limit_per_month === null;
    }
}
