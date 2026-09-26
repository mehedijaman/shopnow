<?php

namespace Modules\PromoCode;

use Modules\Support\BaseServiceProvider;

class PromoCodeServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }
}
