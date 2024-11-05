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
        // ->withQueryString()
        // ->through(fn($product) => [
        //     'id' => $product->id,
        //     'image_url' => $product->image_url,
        //     'name' => $product->name,
        //     'price' => $product->price,
        //     'sale_price' => $product->sale_price,
        //     'quantity' => $product->quantity,
        //     'unit' => $product->unit,
        //     'min_order' => $product->min_order,
        //     'active' => $product->active,
        //     'featured' => $product->featured,
        //     'category' => $product->category,
        //     'tags' => $product->tags
        // ]);

        return view('index::index', compact('products'));
    }

    public function contact()
    {
        return view('contact');
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
