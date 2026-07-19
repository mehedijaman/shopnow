<?php

namespace Modules\Order\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Order\Models\Order;
use Modules\Support\Http\Controllers\BackendController;

class OrderReportController extends BackendController
{
    public function index(Request $request): Response
    {
        $from = $request->input('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();

        $to = $request->input('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Aggregate queries for performance
        $summary = [
            'totalOrders' => Order::whereBetween('created_at', [$from, $to])->count(),
            'totalRevenue' => round(Order::whereBetween('created_at', [$from, $to])
                ->whereNotIn('status', ['cancelled'])->sum('total'), 2),
            'avgOrderValue' => round(Order::whereBetween('created_at', [$from, $to])
                ->whereNotIn('status', ['cancelled'])->avg('total') ?? 0, 2),
            'pendingCount' => Order::whereBetween('created_at', [$from, $to])
                ->where('status', 'pending')->count(),
            'completedCount' => Order::whereBetween('created_at', [$from, $to])
                ->whereIn('status', ['delivered', 'completed'])->count(),
            'cancelledCount' => Order::whereBetween('created_at', [$from, $to])
                ->where('status', 'cancelled')->count(),
        ];

        // Daily breakdown using DB grouping
        $dailyRevenueRows = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('created_at, total, status')
            ->get();

        $dailyGroups = $dailyRevenueRows
            ->groupBy(fn ($o) => Carbon::parse($o->created_at)->format('Y-m-d'))
            ->map(fn ($orders) => [
                'count' => $orders->count(),
                'revenue' => round($orders->where('status', '!=', 'cancelled')->sum('total'), 2),
            ]);

        $period = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m-d');
            $group = $dailyGroups->get($key);
            $period[] = [
                'date' => $cursor->format('d M'),
                'count' => $group ? (int) $group['count'] : 0,
                'revenue' => round($group ? (float) $group['revenue'] : 0, 2),
            ];
            $cursor->addDay();
        }

        // Status breakdown using DB grouping
        $statusGroups = Order::whereBetween('created_at', [$from, $to])
            ->selectRaw('status, count(*) as count, sum(total) as revenue')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $byStatus = collect(['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'])
            ->mapWithKeys(fn ($status) => [
                $status => [
                    'count' => $statusGroups->get($status)?->count ?? 0,
                    'revenue' => round((float) ($statusGroups->get($status)?->revenue ?? 0), 2),
                ],
            ]);

        $paginated = Order::whereBetween('created_at', [$from, $to])
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString()
            ->through(fn ($o) => [
                'id' => $o->id,
                'name' => $o->name,
                'phone' => $o->phone,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'total' => $o->total,
                'created_at' => Carbon::parse($o->created_at)->format('d M Y'),
            ]);

        return inertia('Order/OrderReport', [
            'summary' => $summary,
            'byStatus' => $byStatus,
            'daily' => $period,
            'orders' => $paginated,
            'filters' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
            ],
        ]);
    }
}
