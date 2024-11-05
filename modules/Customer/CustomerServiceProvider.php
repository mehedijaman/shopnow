<?php

namespace Modules\Customer;

use Modules\Customer\Models\Customer;
use Modules\Customer\Observers\CustomerObserver;
use Modules\Support\BaseServiceProvider;

class CustomerServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'customer');

        Customer::observe(CustomerObserver::class);
    }
}
