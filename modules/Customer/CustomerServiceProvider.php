<?php

namespace Modules\Customer;

use Modules\Support\BaseServiceProvider;

class CustomerServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'customer');
    }
}
