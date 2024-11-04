<?php

namespace Modules\Cart;

use Modules\Support\BaseServiceProvider;

class CartServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'cart');
    }
}
