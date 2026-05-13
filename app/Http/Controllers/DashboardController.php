<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(DashboardMetricsService $metrics): View
    {
        return view('dashboard', $metrics->forUser(auth()->user()));
    }
}
