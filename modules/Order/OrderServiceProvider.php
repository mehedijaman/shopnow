<?php

namespace Modules\Order;

use Modules\Order\Models\Order;
use Modules\Order\Observers\OrderSmsObserver;
use Modules\Support\BaseServiceProvider;

class OrderServiceProvider extends BaseServiceProvider
{
    protected ?string $viewNamespace = 'order';

    public function boot()
    {
        parent::boot();

        Order::observe(OrderSmsObserver::class);

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'order');
    }
}
