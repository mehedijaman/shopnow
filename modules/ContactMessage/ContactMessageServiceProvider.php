<?php

namespace Modules\ContactMessage;

use Modules\Support\BaseServiceProvider;

class ContactMessageServiceProvider extends BaseServiceProvider
{
    protected ?string $viewNamespace = 'contactMessage';

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'contactMessage');
    }
}
