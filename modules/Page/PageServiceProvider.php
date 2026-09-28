<?php

namespace Modules\Page;

use Modules\Support\BaseServiceProvider;

class PageServiceProvider extends BaseServiceProvider
{
    protected ?string $viewNamespace = 'page';

    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'page');
    }
}
