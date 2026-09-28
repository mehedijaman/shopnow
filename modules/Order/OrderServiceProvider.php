<?php

namespace Modules\Order;

use Modules\Support\BaseServiceProvider;

class OrderServiceProvider extends BaseServiceProvider
{
    protected ?string $viewNamespace = 'order';

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'order');
    }
}
