<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;

class DashboardMetricsService
{
    /**
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        $tenant = Tenant::query()->where('email', $user->email)->first();

        $service = DolibarrService::forTenant($tenant);

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        $metrics = $service->estimatePosMetrics($monthStart, $monthEnd, $now);

        $avgTicket = $metrics['invoice_count_month'] > 0
            ? (int) round($metrics['revenue_month_cents'] / $metrics['invoice_count_month'])
            : 0;

        return [
            'tenant' => $tenant,
            'planLabel' => $tenant?->plan,
            'salesToday' => $metrics['sales_today'],
            'revenueMonthCents' => $metrics['revenue_month_cents'],
            'averageTicketCents' => $avgTicket,
        ];
    }
}
