<?php

namespace Modules\Dashboard\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductBrand;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductTag;
use Modules\Support\Http\Controllers\BackendController;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends BackendController
{
    /**
     * Display the dashboard view.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $products = Product::all();
        $productCategories = ProductCategory::all();

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

        return Inertia::render('Dashboard/DashboardIndex', [
            'count' => $count,
        ]);
    }
}
