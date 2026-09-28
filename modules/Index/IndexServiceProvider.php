<?php

namespace Modules\Index;

use Modules\Support\BaseServiceProvider;

class IndexServiceProvider extends BaseServiceProvider
{
    protected $namespace = 'Modules\Index\Http\Controllers';

    protected ?string $viewNamespace = 'index';

    public function boot()
    {
        parent::boot();

        $this->loadViewsFrom(__DIR__.'/views', $this->viewNamespace);
    }
}
