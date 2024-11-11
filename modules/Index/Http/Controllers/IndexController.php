<?php

namespace Modules\Index\Http\Controllers;

use Modules\Product\Models\Product;
use Modules\Support\Http\Controllers\SiteController;

class IndexController extends SiteController
{
    public function index()
    {
        $products = Product::with(['category', 'tags'])
            ->orderBy('id', 'desc')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 12));

        return view('index::index', compact('products'));
    }

    public function about()
    {
        return view('about');
    }

    public function privacyPolicy()
    {
        return view('privacy-policy');
    }

    public function termsOfService()
    {
        return view('terms-of-service');
    }
}
