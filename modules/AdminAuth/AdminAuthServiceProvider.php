<?php

namespace Modules\AdminAuth;

use Modules\Support\BaseServiceProvider;

class AdminAuthServiceProvider extends BaseServiceProvider
{
    /**
     * View + translation namespace for this module.
     */
    protected ?string $viewNamespace = 'admin-auth';

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Modules\AdminAuth\Http\Controllers';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', $this->viewNamespace);
        parent::boot();
    }
}
