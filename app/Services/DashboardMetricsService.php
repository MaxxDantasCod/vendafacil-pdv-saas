<?php

namespace App\Services;

use App\Models\Produto;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
     * @return array<string, mixed>
     */
    public function forUser(User $user): array
    {
        $tenant = $user->tenant_id ? Tenant::find($user->tenant_id) : null;

        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();

        if ($tenant) {
            $cacheKey = sprintf('dashboard_metrics:tenant:%s:%s', $tenant->id, $now->format('Y-m-d-H'));

            $cached = Cache::remember($cacheKey, 60, function () use ($tenant, $monthStart, $monthEnd, $now) {
                $service = DolibarrService::forTenant($tenant);

                $metrics = $service->estimatePosMetrics($monthStart, $monthEnd, $now);

                $avgTicket = $metrics['invoice_count_month'] > 0
                    ? (int) round($metrics['revenue_month_cents'] / $metrics['invoice_count_month'])
                    : 0;

                // aggregated stock query in a single hit
                $stockAgg = DB::table('produtos')
                    ->where('tenant_id', $tenant->id)
                    ->selectRaw('COALESCE(SUM(stock_quantity), 0) as inventoryCount, COALESCE(SUM(CASE WHEN stock_quantity IS NOT NULL AND stock_quantity <= 5 THEN 1 ELSE 0 END), 0) as lowStockCount')
                    ->first();

                // sales and revenue from Dolibarr
                $salesTodayCount = $metrics['sales_today'] ?? 0;
                $salesTodayAmount = $metrics['revenue_today_cents'] ?? 0;
                $salesMonthAmount = $metrics['revenue_month_cents'] ?? 0;
                $salesMonthCount = $metrics['invoice_count_month'] ?? 0;

                // fallback local sales from caixas if Dolibarr is unavailable or returns zeros
                if ($salesTodayCount === 0 && $salesTodayAmount === 0) {
                    $caixaToday = DB::table('caixas')
                        ->where('tenant_id', $tenant->id)
                        ->whereDate('aberto_em', now()->toDateString())
                        ->selectRaw('COALESCE(SUM(total_vendas), 0) as totalVendas, COUNT(CASE WHEN total_vendas > 0 THEN 1 END) as vendasCount')
                        ->first();

                    $salesTodayAmount = (int) round(($caixaToday->totalVendas ?? 0) * 100);
                    $salesTodayCount = (int) ($caixaToday->vendasCount ?? 0);
                }

                if ($salesMonthAmount === 0 && $salesMonthCount === 0) {
                    $caixaMonth = DB::table('caixas')
                        ->where('tenant_id', $tenant->id)
                        ->whereDate('aberto_em', '>=', $monthStart->toDateString())
                        ->whereDate('aberto_em', '<=', $monthEnd->toDateString())
                        ->selectRaw('COALESCE(SUM(total_vendas), 0) as totalVendas, COUNT(CASE WHEN total_vendas > 0 THEN 1 END) as vendasCount')
                        ->first();

                    $salesMonthAmount = (int) round(($caixaMonth->totalVendas ?? 0) * 100);
                    $salesMonthCount = (int) ($caixaMonth->vendasCount ?? 0);
                }

                return [
                    'metrics' => [
                        'sales_today_count' => $salesTodayCount,
                        'sales_today_amount_cents' => $salesTodayAmount,
                        'sales_month_count' => $salesMonthCount,
                        'sales_month_amount_cents' => $salesMonthAmount,
                        'invoice_count_month' => $metrics['invoice_count_month'] ?? 0,
                    ],
                    'avgTicket' => $avgTicket,
                    'inventoryCount' => (int) ($stockAgg->inventoryCount ?? 0),
                    'lowStockCount' => (int) ($stockAgg->lowStockCount ?? 0),
                ];
            });

            $metrics = $cached['metrics'];
            $avgTicket = $cached['avgTicket'];
            $inventoryCount = $cached['inventoryCount'];
            $lowStockCount = $cached['lowStockCount'];
        } else {
            $service = null;
            $metrics = ['sales_today_count' => 0, 'sales_today_amount_cents' => 0, 'sales_month_count' => 0, 'sales_month_amount_cents' => 0];
            $avgTicket = 0;
            $inventoryCount = 0;
            $lowStockCount = 0;
        }

        return [
            'tenant' => $tenant,
            'planLabel' => $tenant?->plan,
            'salesTodayCount' => $metrics['sales_today_count'] ?? 0,
            'salesTodayAmountCents' => $metrics['sales_today_amount_cents'] ?? 0,
            'salesMonthCount' => $metrics['sales_month_count'] ?? 0,
            'salesMonthAmountCents' => $metrics['sales_month_amount_cents'] ?? 0,
            'averageTicketCents' => $avgTicket,
            'inventoryCount' => $inventoryCount,
            'lowStockCount' => $lowStockCount,
        ];
    }
}
