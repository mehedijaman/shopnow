<?php

namespace Modules\Index\Http\Controllers;

use Modules\Product\Models\Product;
use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index()
    {
        $products = Product::with(['category', 'tags'])->orderBy('id', 'desc')->get();
        // ->search(request('searchContext'), request('searchTerm'))
        // ->paginate(request('rowsPerPage', 8));

        return  view('index::index', compact('products'));
    }

    public function shop()
    {
        $products = Product::orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 8));

        return  view('index::shop', compact('products'));
    }
}
