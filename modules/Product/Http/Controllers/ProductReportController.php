<?php

namespace Modules\Product\Http\Controllers;

use Inertia\Response;
use Modules\Order\Models\OrderProduct;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Support\Http\Controllers\BackendController;

class ProductReportController extends BackendController
{
    public function index(): Response
    {
        $products = Product::all(['active', 'featured', 'quantity', 'category_id']);

        $summary = [
            'totalProducts' => $products->count(),
            'activeProducts' => $products->where('active', true)->count(),
            'inactiveProducts' => $products->where('active', false)->count(),
            'featuredProducts' => $products->where('featured', true)->count(),
        ];

        $topSelling = OrderProduct::selectRaw('product_id, sum(quantity) as sold_qty, sum(total_price) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('sold_qty')
            ->limit(10)
            ->with('product:id,name,category_id')
            ->get()
            ->map(fn ($op) => [
                'product_name' => $op->product?->name ?? 'Product #'.$op->product_id,
                'category' => $op->product?->category?->name ?? '—',
                'sold_qty' => (int) $op->sold_qty,
                'revenue' => round($op->revenue, 2),
            ]);

        $lowStock = Product::where('active', true)
            ->where('quantity', '<', 10)
            ->orderBy('quantity')
            ->limit(15)
            ->with('category:id,name')
            ->get(['id', 'name', 'quantity', 'category_id'])
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'quantity' => $p->quantity,
                'category' => $p->category?->name ?? '—',
            ]);

        $byCategory = ProductCategory::withCount('products')
            ->orderByDesc('products_count')
            ->limit(10)
            ->get(['id', 'name'])
            ->map(fn ($c) => [
                'name' => $c->name,
                'count' => $c->products_count,
            ]);

        return inertia('Product/ProductReport', [
            'summary' => $summary,
            'topSelling' => $topSelling,
            'lowStock' => $lowStock,
            'byCategory' => $byCategory,
        ]);
    }
}
