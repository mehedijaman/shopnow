<?php

namespace Modules\Customer\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Modules\Customer\Models\Customer;
use Modules\Support\Http\Controllers\BackendController;

class CustomerReportController extends BackendController
{
    public function index(): Response
    {
        $customers = Customer::withoutTrashed()->get(['id', 'active', 'created_at']);

        $totalSpent = (float) DB::table('orders')
            ->whereNull('deleted_at')
            ->sum('total');

        $summary = [
            'totalCustomers' => $customers->count(),
            'activeCustomers' => $customers->where('active', true)->count(),
            'inactiveCustomers' => $customers->where('active', false)->count(),
            'totalSpent' => round($totalSpent, 2),
        ];

        $topSpenders = Customer::withoutTrashed()
            ->select('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.created_at')
            ->selectRaw('COALESCE(SUM(orders.total), 0) as total_spent')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.customer_id', '=', 'customers.id')
                    ->whereNull('orders.deleted_at');
            })
            ->groupBy('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.created_at')
            ->orderByDesc('total_spent')
            ->limit(10)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'email' => $c->email,
                'phone' => $c->phone,
                'total_spent' => round($c->total_spent, 2),
                'joined' => Carbon::parse($c->created_at)->format('d M Y'),
            ]);

        // Monthly new customers — last 12 months
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

        $paginated = Customer::withoutTrashed()
            ->select('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.active', 'customers.created_at')
            ->selectRaw('COALESCE(SUM(orders.total), 0) as total_spent')
            ->leftJoin('orders', function ($join) {
                $join->on('orders.customer_id', '=', 'customers.id')
                    ->whereNull('orders.deleted_at');
            })
            ->groupBy('customers.id', 'customers.name', 'customers.phone', 'customers.email', 'customers.active', 'customers.created_at')
            ->orderByDesc('total_spent')
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
