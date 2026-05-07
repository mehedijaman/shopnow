<?php

namespace Modules\Customer\Http\Controllers;

use Carbon\Carbon;
use Inertia\Response;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Controllers\BackendController;

class CustomerReportController extends BackendController
{
    public function index(): Response
    {
        $customers = Customer::withTrashed(false)->get(['active', 'total_spent', 'created_at']);

        $summary = [
            'totalCustomers' => $customers->count(),
            'activeCustomers' => $customers->where('active', true)->count(),
            'inactiveCustomers' => $customers->where('active', false)->count(),
            'totalSpent' => round($customers->sum('total_spent'), 2),
        ];

        $topSpenders = Customer::orderByDesc('total_spent')
            ->limit(10)
            ->get(['id', 'name', 'phone', 'email', 'total_spent', 'created_at'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'total_spent' => round($c->total_spent, 2),
                'joined' => Carbon::parse($c->created_at)->format('d M Y'),
            ]);

        // Monthly new customers — last 12 months (PHP groupBy, SQLite-safe)
        $now = Carbon::now();
        $twelveMonthsAgo = $now->copy()->subMonths(11)->startOfMonth();
        $recentCustomers = Customer::where('created_at', '>=', $twelveMonthsAgo)->get(['created_at']);

        $monthLabels = [];
        $monthData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $key = $month->format('Y-m');
            $monthLabels[] = $month->format('M Y');
            $monthData[] = $recentCustomers
                ->filter(fn ($c) => Carbon::parse($c->created_at)->format('Y-m') === $key)
                ->count();
        }

        $paginated = Customer::orderByDesc('total_spent')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'total_spent' => round($c->total_spent, 2),
                'active' => $c->active,
                'joined' => Carbon::parse($c->created_at)->format('d M Y'),
            ]);

        return inertia('Customer/CustomerReport', [
            'summary' => $summary,
            'topSpenders' => $topSpenders,
            'monthlyNew' => ['labels' => $monthLabels, 'data' => $monthData],
            'customers' => $paginated,
        ]);
    }
}
