<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use Modules\Customer\Models\Customer;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductTag;
use Modules\Support\Http\Controllers\BackendController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends BackendController
{
    public function index()
    {
        $now = Carbon::now();
        $monthStart = $now->copy()->startOfMonth();

        // Load all orders once for collection-based aggregations
        $allOrders = Order::whereNotIn('status', ['cancelled'])->get(['status', 'total', 'created_at']);

        $monthlyRevenue = $allOrders
            ->filter(fn ($o) => Carbon::parse($o->created_at)->gte($monthStart))
            ->sum('total');

        // Build last-12-months revenue array (SQLite-safe: PHP groupBy)
        $twelveMonthsAgo = $now->copy()->subMonths(11)->startOfMonth();
        $ordersForChart = Order::whereNotIn('status', ['cancelled'])
            ->where('created_at', '>=', $twelveMonthsAgo)
            ->get(['total', 'created_at']);

        $revenueLabels = [];
        $revenueData = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $key = $month->format('Y-m');
            $revenueLabels[] = $month->format('M Y');
            $revenueData[] = round(
                $ordersForChart
                    ->filter(fn ($o) => Carbon::parse($o->created_at)->format('Y-m') === $key)
                    ->sum('total'),
                2
            );
        }

        // Orders by status
        $ordersByStatus = Order::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Recent orders
        $recentOrders = Order::orderByDesc('id')
            ->limit(10)
            ->get(['id', 'name', 'phone', 'status', 'payment_status', 'total', 'created_at'])
            ->map(fn ($o) => [
                'id' => $o->id,
                'name' => $o->name,
                'phone' => $o->phone,
                'status' => $o->status,
                'payment_status' => $o->payment_status,
                'total' => $o->total,
                'created_at' => Carbon::parse($o->created_at)->format('d M Y'),
            ]);

        // Top products by qty sold
        $topProducts = OrderProduct::selectRaw('product_id, sum(quantity) as sold_qty, sum(total_price) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('sold_qty')
            ->limit(5)
            ->with('product:id,name')
            ->get()
            ->map(fn ($op) => [
                'product_name' => $op->product?->name ?? 'Product #'.$op->product_id,
                'sold_qty' => (int) $op->sold_qty,
                'revenue' => round($op->revenue, 2),
            ]);

        $products = Product::all(['active', 'featured']);
        $productCategories = ProductCategory::all(['active', 'featured']);

        $count = [
            'totalProducts' => $products->count(),
            'activeProducts' => $products->where('active', true)->count(),
            'inactiveProducts' => $products->where('active', false)->count(),
            'featuredProducts' => $products->where('featured', true)->count(),
            'totalProductCategories' => $productCategories->count(),
            'activeProductCategories' => $productCategories->where('active', true)->count(),
            'inactiveProductCategories' => $productCategories->where('active', false)->count(),
            'featuredProductCategories' => $productCategories->where('featured', true)->count(),
            'totalProductTags' => ProductTag::count(),
            'totalProductBrands' => ProductBrand::count(),
            'users' => User::count(),
            'permissions' => Permission::count(),
            'roles' => Role::count(),
        ];

        $kpi = [
            'totalRevenue' => round($allOrders->sum('total'), 2),
            'monthRevenue' => round($monthlyRevenue, 2),
            'totalOrders' => Order::count(),
            'monthOrders' => Order::where('created_at', '>=', $monthStart)->count(),
            'totalCustomers' => Customer::count(),
            'totalProducts' => $products->count(),
        ];

        return Inertia::render('Dashboard/DashboardIndex', [
            'count' => $count,
            'kpi' => $kpi,
            'ordersByStatus' => $ordersByStatus,
            'monthlyRevenue' => ['labels' => $revenueLabels, 'data' => $revenueData],
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
        ]);
    }
}
