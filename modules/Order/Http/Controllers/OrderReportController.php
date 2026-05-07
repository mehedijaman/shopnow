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

        $orders = Order::whereBetween('created_at', [$from, $to])->get();

        $nonCancelled = $orders->whereNotIn('status', ['cancelled']);

        // Daily breakdown (PHP groupBy — SQLite-safe)
        $dailyGroups = $orders->groupBy(fn ($o) => Carbon::parse($o->created_at)->format('Y-m-d'));

        $period = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->format('Y-m-d');
            $group = $dailyGroups->get($key, collect());
            $period[] = [
                'date' => $cursor->format('d M'),
                'count' => $group->count(),
                'revenue' => round($group->whereNotIn('status', ['cancelled'])->sum('total'), 2),
            ];
            $cursor->addDay();
        }

        $byStatus = collect(['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled'])
            ->mapWithKeys(fn ($status) => [
                $status => [
                    'count' => $orders->where('status', $status)->count(),
                    'revenue' => round($orders->where('status', $status)->sum('total'), 2),
                ],
            ]);

        $summary = [
            'totalOrders' => $orders->count(),
            'totalRevenue' => round($nonCancelled->sum('total'), 2),
            'avgOrderValue' => $nonCancelled->count()
                ? round($nonCancelled->sum('total') / $nonCancelled->count(), 2)
                : 0,
            'pendingCount' => $orders->where('status', 'pending')->count(),
            'completedCount' => $orders->whereIn('status', ['delivered', 'completed'])->count(),
            'cancelledCount' => $orders->where('status', 'cancelled')->count(),
        ];

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
