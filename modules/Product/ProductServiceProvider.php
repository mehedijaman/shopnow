<?php

namespace Modules\Product;

use Illuminate\Support\Facades\Event;
use Modules\Product\Listeners\GrantDownloadsOnPayment;
use Modules\Product\Models\Product;
use Modules\Product\Observers\ProductObserver;
use Modules\Support\BaseServiceProvider;
use Modules\Support\Events\OrderPaymentConfirmed;

class ProductServiceProvider extends BaseServiceProvider
{
    protected $namespace = 'Modules\Product\Http\Controllers';

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'product');

        Product::observe(ProductObserver::class);

        Event::listen(
            OrderPaymentConfirmed::class,
            GrantDownloadsOnPayment::class,
        );
    }
}
