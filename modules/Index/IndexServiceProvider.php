<?php

namespace Modules\Index;

use Illuminate\Support\Facades\View;
use Modules\Product\Services\Site\GetProductCategoryOptions;
use Modules\Support\BaseServiceProvider;

class IndexServiceProvider extends BaseServiceProvider
{
    protected $namespace = 'Modules\Index\Http\Controllers';

    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__ . '/views', 'index');

        $categories = GetProductCategoryOptions::get();
        View::share('categories', $categories);
    }
}
