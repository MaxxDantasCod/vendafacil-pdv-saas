<?php
namespace App\Http\Controllers;

class PlanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $currentPlan = $user->tenant->plan ?? 'free';
        
        $usage = \App\Models\SalesUsageMonthly::where('user_id', $user->id)
            ->where('year_month', now()->format('Y-m'))
            ->value('sales_count') ?? 0;

        return view('planos.index', compact('currentPlan', 'usage'));
    }
}