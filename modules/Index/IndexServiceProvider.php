<?php

namespace Modules\Index;

use Illuminate\Support\Facades\View;
use Modules\Product\Models\ProductCategory;
use Modules\Support\BaseServiceProvider;

class IndexServiceProvider extends BaseServiceProvider
{
    protected $namespace = 'Modules\Index\Http\Controllers';

    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/views', 'index');

        $categories = ProductCategory::orderBy('name')->get();
        View::share('categories', $categories);
    }
}
