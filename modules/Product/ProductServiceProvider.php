<?php

namespace Modules\Product;

use Modules\Support\BaseServiceProvider;

class ProductServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'product');
    }
}