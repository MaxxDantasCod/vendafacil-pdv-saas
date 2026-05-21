<?php

namespace App\Services;

use App\Models\Tenant;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DolibarrService
{
    public function __construct(
        protected string $baseUrl,
        protected string $apiKey,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            (string) config('dolibarr.base_url'),
            (string) (config('dolibarr.api_key') ?? ''),
        );
    }

    public static function forTenant(?Tenant $tenant): self
    {
        if ($tenant === null) {
            return self::fromConfig();
        }

        return new self(
            rtrim($tenant->dolibarr_url, '/'),
            $tenant->api_key ?: (string) (config('dolibarr.api_key') ?? ''),
        );
    }

    /**
     * Lista produtos via API REST Dolibarr (módulo Products).
     *
     * @return array<int, mixed>
     */
    public function getProducts(array $query = []): array
    {
        $defaults = [
            'sortfield' => 't.rowid',
            'sortorder' => 'DESC',
            'limit' => 100,
        ];

        $data = $this->get('/products', array_merge($defaults, $query));

        if ($data === null) {
            return [];
        }

        if (isset($data[0]) || array_is_list($data)) {
            return $data;
        }

        return $data['data'] ?? [];
    }

    /**
     * Métricas estimadas a partir de /invoices (ajuste conforme sua versão Dolibarr).
     *
     * @return array{sales_today: int, revenue_month_cents: int, invoice_count_month: int}
     */
    public function estimatePosMetrics(CarbonInterface $monthStart, CarbonInterface $monthEnd, CarbonInterface $today): array
    {
        $data = $this->get('/invoices', [
            'sortfield' => 't.rowid',
            'sortorder' => 'DESC',
            'limit' => 500,
        ]);

        if (! is_array($data)) {
            return ['sales_today' => 0, 'revenue_month_cents' => 0, 'invoice_count_month' => 0];
        }

        $invoices = isset($data[0]) || array_is_list($data) ? $data : ($data['data'] ?? []);

        if (! is_array($invoices)) {
            return ['sales_today' => 0, 'revenue_month_cents' => 0, 'invoice_count_month' => 0];
        }

        $salesToday = 0;
        $revenueToday = 0;
        $revenueMonth = 0;
        $countMonth = 0;
        $todayStr = $today->toDateString();

        foreach ($invoices as $inv) {
            if (! is_array($inv)) {
                continue;
            }

            $date = $inv['date'] ?? $inv['date_invoice'] ?? null;
            $total = isset($inv['total_ttc'])
                ? (float) $inv['total_ttc']
                : (isset($inv['total_ht']) ? (float) $inv['total_ht'] : 0.0);

            if ($date === null || $date === '') {
                continue;
            }

            $dateOnly = is_numeric($date)
                ? date('Y-m-d', (int) $date)
                : substr((string) $date, 0, 10);

            if ($dateOnly === $todayStr) {
                $salesToday++;
                $revenueToday += (int) round($total * 100);
            }

            if ($dateOnly >= $monthStart->toDateString() && $dateOnly <= $monthEnd->toDateString()) {
                $revenueMonth += (int) round($total * 100);
                $countMonth++;
            }
        }

        return [
            'sales_today' => $salesToday,
            'revenue_today_cents' => $revenueToday,
            'revenue_month_cents' => $revenueMonth,
            'invoice_count_month' => $countMonth,
        ];
    }

    public function get(string $endpoint, array $query = []): ?array
    {
        $url = $this->buildApiUrl($endpoint);

        try {
            $response = Http::timeout(20)
                ->withHeaders(['DOLAPIKEY' => $this->apiKey])
                ->acceptJson()
                ->get($url, $query);

            if (! $response->successful()) {
                Log::warning('Dolibarr API HTTP error', [
                    'status' => $response->status(),
                    'url' => $url,
                ]);

                return null;
            }

            return $response->json();
        } catch (\Throwable $e) {
            Log::warning('Dolibarr API exception', ['message' => $e->getMessage(), 'url' => $url]);

            return null;
        }
    }

    protected function buildApiUrl(string $endpoint): string
    {
        $endpoint = '/'.ltrim($endpoint, '/');
        $base = rtrim($this->baseUrl, '/');

        return $base.'/api/index.php'.$endpoint;
    }
}
