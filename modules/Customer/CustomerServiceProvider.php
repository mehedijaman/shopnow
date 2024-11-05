<?php

namespace Modules\Customer;

use Modules\Customer\Models\Customer;
use Modules\Support\BaseServiceProvider;
use Modules\Customer\Observers\CustomerObserver;

class CustomerServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/views', 'customer');

        Customer::observe(CustomerObserver::class);
    }
}
