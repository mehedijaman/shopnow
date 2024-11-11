<?php

namespace Modules\ContactMessage;

use Modules\Support\BaseServiceProvider;

class ContactMessageServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'contactMessage');
    }
}
