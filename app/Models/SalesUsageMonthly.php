<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesUsageMonthly extends Model
{
    use HasFactory;

    protected $table = 'sales_usage_monthly';

    protected $fillable = [
        'user_id',
        'year_month',
        'sales_count',
    ];

    protected $casts = [
        'sales_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
