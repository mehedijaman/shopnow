<?php

namespace Modules\Profile;

use Modules\Support\BaseServiceProvider;

class ProfileServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
    }
}
