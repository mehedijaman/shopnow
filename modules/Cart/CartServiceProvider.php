<?php

namespace Modules\Cart;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Modules\Cart\Listeners\MergeGuestCartOnLogin;
use Modules\Support\BaseServiceProvider;

class CartServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadViewsFrom(__DIR__.'/views', 'cart');

        Event::listen(Login::class, MergeGuestCartOnLogin::class);
    }
}
