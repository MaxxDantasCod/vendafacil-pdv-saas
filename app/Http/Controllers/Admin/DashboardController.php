<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $tenantCount = Tenant::count();
        $userCount = User::count();
        $plans = Tenant::query()
            ->selectRaw('plan, count(*) as total')
            ->groupBy('plan')
            ->pluck('total', 'plan')
            ->toArray();

        return view('admin.dashboard', compact('tenantCount', 'userCount', 'plans'));
    }
}
